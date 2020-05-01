<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Lib;

use Spool\Pedis\Exceptions\PedisException;
use Spool\Pedis\Constants\ErrorCode;
use Spool\Pedis\Commands\Commands;

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
	'startBefore'	 => [],
	'startAfter'	 => [],
	'endBefore'	 => [],
	'endAfter'	 => []
    ];

    /**
     * 命令处理
     * @var Commands
     */
    protected $commands;

    /**
     * 单例模式实例
     * @var type 
     */
    private static $one;
    /**
     * 客户端键
     * @var int
     */
    private static $clientKey = 1;

    /**
     * 客户端列表
     * @var array
     */
    private $client		 = [];
    private $existsWatchers	 = [];
    private $nodeWatchers	 = [];
    private $clientWatchers	 = [];

    private function __construct()
    {
	
    }

    /**
     * 单例模式
     * @param \Spool\Pedis\Lib\Config $config
     * @param array $event
     * @return \Spool\Pedis\Lib\PedisServer
     */
    public static function Init(Config &$config, array $event): PedisServer
    {
	if (self::$one) {
	    return self::$one;
	}
	self::$one		 = new PedisServer();
	self::$one->config	 = $config;
	self::$one->event	 = $event;
	self::$one->commands	 = new Commands($config);
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
	$sndbuf	 = socket_get_option($sock, SOL_SOCKET, SO_SNDBUF);
	$rcvbuf	 = socket_get_option($sock, SOL_SOCKET, SO_RCVBUF);
	Log::debug("send buffer size(写缓存区大小):" . $sndbuf / 1024 . "}m \n");
	$log	 = Log::debug("receive buffer size(写缓存区大小):" . $rcvbuf / 1024 . "}m \n");
	Log::debug("日志打印返回了: {$log}\n");
	$this->workNode($sock);
	foreach ($this->event['endBefore'] as $callInfo) {
	    call_user_func($callInfo['callBack'], $callInfo['params'] ?? NULL);
	}
    }

    private function workNode(&$sock)
    {
	$baseip			 = $baseport		 = NULL;
	socket_getsockname($sock, $baseip, $baseport);
	$baseKey		 = $baseip . ':' . $baseport;
	$this->clients[$baseKey] = $sock;
	while (true) {
	    $read	 = $this->clients;
	    $write	 = $except	 = NULL;
	    // get a list of all the clients that have data to be read from
	    // if there are no clients with data, go to next iteration
	    Log::debug("正在等待读取socket!\n");
	    socket_select($read, $write, $except, NULL);
	    Log::debug("读取到数据了socket!\n");
	    // check if there is a client trying to connect
	    if (in_array($sock, $read, TRUE)) {
		// accept the client, and add him to the $clients array
		$newkey	 = $this->addClient($sock);
		Log::debug("New client connected: {$newkey}\n");
		// remove the listening socket from the clients-with-data array
		$key	 = array_search($sock, $read, TRUE);
		unset($read[$key]);
	    }
//            break;
	    // loop through all the clients that have data to read from
	    foreach ($read as $read_sock) {
		$key		 = array_search($read_sock, $this->clients, TRUE);
		// read until newline or 1024 bytes
		// socket_read while show errors when the client is disconnected, so silence the error messages
		$string_read	 = '';
		if (!$this->config->openLengthCheck) {
		    $array_read	 = $this->readByEof($read_sock, $key);
		    $string_read	 = $array_read['data'];
		    $len_read	 = $array_read['len'];
		} else {
		    try {
			$len_read	 = socket_recv($read_sock, $string_read, $this->config->packageMaxLength, MSG_DONTWAIT);
			$len_read	 = strlen($string_read);
			if ($len_read > 0) {
			    Log::debug($string_read . "\n");
			}
		    } catch (PedisException $exc) {
			throw $exc;
		    }
		}
		// check if the client is disconnected
		if ($len_read === false) {
		    // no data
		    continue;
		} else if (0 === $len_read) {
		    // remove client for $clients array
//		    $key = array_search($read_sock, $this->clients, TRUE);
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
		if ($data === 'quit') {
		    socket_close($this->clients[$key]);
		    unset($this->clients[$key]);
		    continue;
		}
		// check if there is any data after trimming off the spaces
		if (!empty($data)) {
		    //do command begin
		    foreach ($this->event['commandBefore'] as $callInfo) {
			call_user_func($callInfo['callBack'], $callInfo['params'] ?? NULL);
		    }
		    try {
			//do command
			$result = $this->commands->doCommand($data, $key);
		    } catch (PedisException $exc) {
			$result = $exc->render($exc);
		    }

		    //do command after
		    foreach ($this->event['commandAfter'] as $callInfo) {
			call_user_func($callInfo['callBack'], $callInfo['params'] ?? NULL);
		    }
		    // send this to all the clients in the $clients array (except the first one, which is a listening socket)
		    foreach ($this->clients as $key => $send_sock) {

			// if its the listening sock or the client that we got the message from, go to the next one in the list
			if ($send_sock == $sock) {
			    continue;
			}
			if ($send_sock == $read_sock) {
			    $send_msg	 = "{$key} get Data: {$data}\n";
			    $send_msg	 .= json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
			    $slen		 = strlen($send_msg);
			    $sendLen	 = socket_send($send_sock, $send_msg, $slen, 0);
//			    $sendLen	 = socket_write($send_sock, $send_msg, $slen);
			    continue;
			}
			Log::debug("{$key} send: {$data}\n");
			$sendMsg = $data . "\n";
			$len	 = strlen($sendMsg);
			// write the message to the client -- add a newline character to the end of the message
			try {
			    //一直过不去,不知道为什么,只能用socket_write来实现了
			    $sendLen = socket_send($send_sock, $sendMsg, $len, 0);
//			    $sendLen = socket_write($send_sock, $sendMsg, $len);
			} catch (PedisException $exc) {
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

    /**
     * 命令处理
     * @param string $data
     */
    private function command(string $data): array
    {
	
    }

    /**
     * 读取结尾检测输入
     * @param type $read_sock
     * @return array
     */
    private function readByEof($read_sock, $key): array
    {
	$len_read = 0;
	try {
	    $string_read = socket_read($read_sock, $this->config->packageMaxLength, PHP_NORMAL_READ);
	    if ($string_read === '') {
		$len_read = false;
	    } elseif ($string_read === false) {
		$len_read = 0;
	    } else {
		$len_read = strlen($string_read);
	    }
	} catch (PedisException | \Throwable $exc) {
	    if ($exc instanceof \Throwable) {
		Log::error('[' . $exc->getCode() . ']' . $exc->getMessage());
		unset($this->clients[$key]);
		$string_read = '';
	    } else {
		throw $exc;
	    }
	}
	return ['len' => $len_read, 'data' => $string_read];
    }

    /**
     * 新增客户端
     * @param type $sock
     * @return string
     */
    private function addClient($sock): string
    {
	// accept the client, and add him to the $clients array
	$newsock		 = socket_accept($sock);
//	$ip			 = NULL;
//	$port			 = NULL;
//	socket_getpeername($newsock, $ip, $port);
//	$newKey			 = $ip . ':' . $port;
	$newKey			 = self::$clientKey++;
	$this->clients[$newKey]	 = $newsock;
	if (self::$clientKey > $this->config->maxclients) {
	    self::$clientKey = 1;
	}
	$welcome = "Welcome to the PEDIS family, where you can do whatever you like.";
	// send the client a welcome message
	socket_write($newsock, $welcome);
	return $newKey;
    }

    /**
     * 绑定IP和port
     * @param type $sock
     * @return bool
     * @throws PedisException
     */
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
