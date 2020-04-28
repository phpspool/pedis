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
 * Description of ProcessMain
 *
 * @author 陈浩波
 */
class BootStrap
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
        'startAfter'  => [],
        'endBefore'   => [],
        'endAfter'    => []
    ];

    /**
     * 单例模式
     * @var BootStrap 
     */
    private static $one;
    
    private $server;

    /**
     * 构建函数,初始化,读取配置信息
     * @param \Spool\Pedis\Lib\Config $config
     * @param void $agvs
     */
    private function __construct()
    {
        
    }

    /**
     * 返回单例对象
     * @param \Spool\Pedis\Lib\Config $config
     * @return \Spool\Pedis\Lib\BootStrap
     */
    public static function Init(Config $config): BootStrap
    {
        if (self::$one) {
            return self::$one;
        }
        self::$one         = new BootStrap();
        self::$one->config = $config;
        return self::$one;
    }

    /**
     * 开始运行
     */
    public function Run()
    {
        $this->checkEvent();
        $pid = $this->Begin();
        if ($pid) {
            $this->writePidFile($pid);
            foreach ($this->event['startBefore'] as $callInfo) {
                call_user_func($callInfo['callBack'], $callInfo['params'] ?? NULL);
            }
            $this->startServer();
            foreach ($this->event['endAfter'] as $callInfo) {
                call_user_func($callInfo['callBack'], $callInfo['params'] ?? NULL);
            }
        }
    }
    /**
     * 启动服务
     * @throws PedisException
     */
    private function startServer()
    {
//        sleep(5);
        $msg = "\nI'm a super process!\n";
        fwrite(STDOUT, $msg);
        $this->server = PedisServer::Init($this->config, $this->event);
        $this->server->serverStart();
        if (is_file($this->config->pidfile)) {
            try {
                unlink($this->config->pidfile);
            } catch (\Exception $exc) {
                throw new PedisException(ErrorCode::PID_FILE_NOT_REMOVE);
            }
        }
    }

    /**
     * 蜕变成守护进程
     * @return int
     * @throws PedisException
     */
    private function Begin(): int
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            /* fork failed */
            throw new PedisException(ErrorCode::FORK_FAILURE);
        } elseif ($pid) {
            /* close the parent */
            return 0;
        } else {
            /* child becomes our daemon */
            $sid = posix_setsid();
            if ($sid < 0) {
                throw new PedisException(ErrorCode::SET_SID_FAILUER);
            }
//            chdir('/');
            umask(0);
            return posix_getpid();
        }
    }

    /**
     * 写入pid文件
     * @param int $pid
     * @return bool
     */
    private function writePidFile(int $pid): bool
    {
        return file_put_contents($this->config->pidfile, $pid) ? TRUE : FALSE;
    }

    /**
     * 检查运行环境
     * @return bool
     * @throws PedisException
     */
    private function checkEvent(): bool
    {
        /**
         * 如果pid文件存在,则不启动
         */
        if (is_file($this->config->pidfile)) {
            throw new PedisException(ErrorCode::PID_FILE_IS_EXISTS);
        }
        /**
         * 开启pcntl异步信号
         */
        if (!pcntl_async_signals()) {
            pcntl_async_signals(true);
        }
        return TRUE;
    }
}
