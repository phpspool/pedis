<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Lib;

use Spool\Pedis\Exceptions\PedisException;
use Spool\Pedis\Constants\ErrorCode;

/**
 * Description of PedisServer
 *
 * @author 陈浩波
 */
class PedisServer
{

    /**
     * 配置信息
     * @var Config
     */
    private $config;

    /**
     * 事件监听
     * @var array
     */
    private $event = [
        'startBefore' => [],
        'startAfter' => [],
        'endBefore' => [],
        'endAfter' => []
    ];
    protected $command;

    /**
     * 单例模式实例
     * @var type 
     */
    private static $one;

    /**
     * 数据实例
     * @var DbNode 
     */
    private $dbNode;

    /**
     * 客户端列表
     * @var array
     */
    private $client = [];
    private $existsWatchers = [];
    private $nodeWatchers = [];
    private $clientWatchers = [];

    private function __construct()
    {
        
    }

    /**
     * 单例模式
     * @param \Spool\Pedis\Lib\Config $config
     * @param array $event
     * @return \Spool\Pedis\Lib\PedisServer
     */
    public static function Init(Config $config, array $event): PedisServer
    {
        if (self::$one) {
            return self::$one;
        }
        self::$one = new PedisServer();
        self::$one->config = $config;
        self::$one->event = $event;
        return self::$one;
    }

    /**
     * 启动服务
     */
    public function serverStart()
    {
        $sock = NULL;
        $this->bindIPAndPort($sock);
        foreach ($this->event['startAfter'] as $callInfo) {
            call_user_func($callInfo['callBack'], $callInfo['params'] ?? NULL);
        }
        $sndbuf = socket_get_option($sock, SOL_SOCKET, SO_SNDBUF);
        $rcvbuf = socket_get_option($sock, SOL_SOCKET, SO_RCVBUF);
        Log::debug("send buffer size(写缓存区大小):" . $sndbuf / 1024 . "}m \n");
        $log = Log::debug("receive buffer size(写缓存区大小):" . $rcvbuf / 1024 . "}m \n");
        Log::debug("日志打印返回了: {$log}\n");
        $this->workNode($sock);
        foreach ($this->event['endBefore'] as $callInfo) {
            call_user_func($callInfo['callBack'], $callInfo['params'] ?? NULL);
        }
    }

    private function workNode(&$sock)
    {
        $baseip = $baseport = NULL;
        socket_getsockname($sock, $baseip, $baseport);
        $baseKey = $baseip . ':' . $baseport;
        $this->clients[$baseKey] = $sock;
        while (true) {
            $read = $this->clients;
            $write = $except = NULL;
            // get a list of all the clients that have data to be read from
            // if there are no clients with data, go to next iteration
            Log::debug("正在等待读取socket!\n");
            socket_select($read, $write, $except, NULL);
            Log::debug("读取到数据了socket!\n");
            // check if there is a client trying to connect
            if (in_array($sock, $read, TRUE)) {
                // accept the client, and add him to the $clients array
                $newkey = $this->addClient($sock);
                Log::debug("New client connected: {$newkey}\n");
                // remove the listening socket from the clients-with-data array
                $key = array_search($sock, $read, TRUE);
                unset($read[$key]);
            }
//            break;
            // loop through all the clients that have data to read from
            foreach ($read as $read_sock) {
                // read until newline or 1024 bytes
                // socket_read while show errors when the client is disconnected, so silence the error messages
                $string_read = '';
                try {
                    $len_read = socket_recv($read_sock, $string_read, 1024, MSG_DONTWAIT);
                    $len_read = strlen($string_read);
                    if ($len_read > 0) {
                        Log::debug($string_read . "\n");
                    }
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                    echo $exc->getMessage();
                    break 2;
                }
                // check if the client is disconnected
                if ($len_read === false) {
                    // no data
                    continue;
                }
                else if (0 === $len_read) {
                    // remove client for $clients array
                    $key = array_search($read_sock, $this->clients, TRUE);
                    unset($this->clients[$key]);
                    Log::debug("{$key} client disconnected.\n");
                    // continue to the next client to read from, if any
                    continue;
                }

                // trim off the trailing/beginning white spaces
                $data = trim($string_read);
                if ($data === 'exit') {
                    break 2;
                }
                // check if there is any data after trimming off the spaces
                if (!empty($data)) {
                    // send this to all the clients in the $clients array (except the first one, which is a listening socket)
                    foreach ($this->clients as $send_sock) {

                        // if its the listening sock or the client that we got the message from, go to the next one in the list
                        if ($send_sock == $sock)
                            continue;
                        if ($send_sock == $read_sock) {
                            $send_msg = "get Data: {$data}\n";
                            $slen = strlen($send_msg);
                            $sendLen = socket_write($send_sock, $send_msg, $slen);
                            continue;
                        }
                        Log::debug("send: {$data}\n");
                        $sendMsg = $data . "\n";
                        $len = strlen($sendMsg);
                        // write the message to the client -- add a newline character to the end of the message
                        try {
                            //一直过不去,不知道为什么,只能用socket_write来实现了
//                            socket_send($send_sock, $sendMsg, $len, MSG_DONTROUTE);
                            $sendLen = socket_write($send_sock, $sendMsg, $len);
                        } catch (\Exception $exc) {
                            echo $exc->getTraceAsString();
                            echo $exc->getMessage();
                            $sendLen = 0;
                        }
                        if ($sendLen) {
                            Log::debug("need send {$len}, send {$sendLen}\n");
                        }
                    } // end of broadcast foreach
                }
            } // end of reading foreach
        }
    }

    private function addClient($sock): string
    {
        // accept the client, and add him to the $clients array
        $newsock = socket_accept($sock);
        $ip = NULL;
        $port = NULL;
        socket_getpeername($newsock, $ip, $port);
        $newKey = $ip . ':' . $port;
        $this->clients[$newKey] = $newsock;

        $welcome = "Welcome to the PEDIS family, where you can do whatever you like.";
        // send the client a welcome message
        socket_write($newsock, $welcome);
        return $newKey;
    }

    private function bindIPAndPort(&$sock): bool
    {
        if (($sock = socket_create(AF_INET, SOCK_STREAM, 0)) < 0) {
            throw new PedisException(ErrorCode::FAILED_TO_CREATE_SOCKET);
        }
        if (($ret = socket_bind($sock, $this->config->host, $this->config->port)) < 0) {
            throw new PedisException(ErrorCode::FAILED_TO_BIND_TO_SOCKET);
        }
        if (( $ret = socket_listen($sock, 0) ) < 0) {
            throw new PedisException(ErrorCode::FAILED_TO_LISTEN_TO_SOCKET);
        }
        socket_set_nonblock($sock);
        return TRUE;
    }

    /**
     * Change the identity to a non-priv user 
     */
    private function change_identity($uid, $gid)
    {
        if (!posix_setgid($gid)) {
            throw new PedisException(ErrorCode::UNABLE_TO_SETGID);
        }

        if (!posix_setuid($uid)) {
            throw new PedisException(ErrorCode::UNABLE_TO_SETUID);
        }
    }

}
