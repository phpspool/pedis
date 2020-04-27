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
     * 构建函数,初始化,读取配置信息
     * @param \Spool\Pedis\Lib\Config $config
     * @param void $agvs
     */
    public function __construct(Config $config, ...$agvs)
    {
        $this->config = $config;
    }

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

    private function startServer()
    {
        foreach ($this->event['startAfter'] as $callInfo) {
            call_user_func($callInfo['callBack'], $callInfo['params'] ?? NULL);
        }
        sleep(10);
        echo "\nI'm a super process!\n";
        foreach ($this->event['endBefore'] as $callInfo) {
            call_user_func($callInfo['callBack'], $callInfo['params'] ?? NULL);
        }
        if (is_file($this->config->pidfile)) {
            try {
                unlink($this->config->pidfile);
            } catch (\Exception $exc) {
                throw new PedisException(ErrorCode::PID_FILE_NOT_REMOVE);
            }
        }
    }

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
        if (is_file($this->config->pidfile)) {
            throw new PedisException(ErrorCode::PID_FILE_IS_EXISTS);
        }
        return TRUE;
    }

}
