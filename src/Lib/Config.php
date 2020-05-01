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
 * Description of Config
 *
 * @author 陈浩波
 */
class Config
{
    //NETWORK
    /**
     * bind ip address
     * @var string
     */
    public $host                    = 'localhost';

    /**
     * port
     * @var int
     */
    public $port                    = 9736;

    /**
     * timeout
     * @var int
     */
    public $timeout                 = 0;

    /**
     *
     * @var int
     */
    public $tcpKeepalive            = 300;
    //GENERAL
    /**
     * daemonize
     * @var bool
     */
    public $daemonize               = false;

    /**
     * pidFile
     * @var string
     */
    public $pidfile                 = '/var/pedis/run/pedis_9736.pid';

    /**
     * loglevel
     * @var string 0-EMERGENCY 1-ALERT 2-CRITICAL 3-ERROR 4-WARNING 5-NOTICE 6-INFO 7-DEBUG 8-ALL
     */
    public $loglevel                = 7;

    /**
     * logfile 
     * @var string
     */
    public $logfile                 = '';

    /**
     * database number
     * @var int
     */
    public $databases               = 16;
    //SNAPSHOTTING
    /**
     * 持久化,同redis,key为时间,value为变化的键值数
     * @var array
     */
    public $save                    = [900 => 1];

    /**
     * 持久化失败时,是否停止接收数据
     * @var bool
     */
    public $stopWritesOnBgsaveError = true;

    /**
     * 快照是否压缩储存
     * @var bool
     */
    public $rdbcompression          = true;

    /**
     * 储存快照后,是否做CRC校验
     * @var bool
     */
    public $rdbchecksum             = true;

    /**
     * 快照的文件名
     * @var string
     */
    public $dbfilename              = 'dump.pdb';

    /**
     * 快照保存的路径
     * @var string
     */
    public $dir                     = '/var/lib/pedis/';
    //REPLICATION 主从同步策略,先不做
    //SECURITY
    /**
     * 命令重命名
     * @var array 
     */
    public $renameCommand           = [];

    /**
     * 设置pedis连接密码
     * @var string
     */
    public $requirepass             = '';
    //CLIENTS
    /**
     * 最大客户端连接数
     * @var int
     */
    public $maxclients              = 65535;
    //MEMORY MANAGEMENT
    /**
     *
     * @var string
     */
    public $maxmemory               = '1024mb';

    /**
     * 内存满时,对现有key的移除策略
     * @var string volatile-lru|allkeys-lru|volatile-random|allkeys-random|volatile-ttl
     */
    public $maxmemoryPolicy         = 'noeviction';
    /**
     * 是否开启末尾检测,默认
     * @var bool
     */
    public $openEofSplit	     = true;
    /**
     * 目前只支持\r\n,不支持别的
     */
//    public $packageEof = "\r\n";
    
    public $packageMaxLength = 1024 * 1024 * 2;
    
    public $openLengthCheck = false;
    public $packageLengthType = 'N';
    public $packageLengthOffset = 0;
    public $packageBodyOffset = 4;

    /**
     * 初始化配置,如果
     * @param array|string $config
     */
    public function __construct($config = null)
    {
        if (is_string($config) && is_file($config)) {
            $readyConfig = parse_ini_file($config, true, INI_SCANNER_TYPED);
        } elseif (is_array($config)) {
            $readyConfig = $config;
        } elseif (!$config) {
            
        } else {
            throw new PedisException(ErrorCode::CONFIG_NOT_STRING_OR_ARRAY);
        }
//        var_dump($readyConfig);exit;
        $this->host                    = $readyConfig["NETWORK"]['host'] ?? $this->host;
        $this->port                    = $readyConfig["NETWORK"]['port'] ?? $this->port;
        $this->timeout                 = $readyConfig["NETWORK"]['timeout'] ?? $this->timeout;
        $this->tcpKeepalive            = $readyConfig["NETWORK"]['tcpKeepalive'] ?? $this->tcpKeepalive;
        $this->daemonize               = $readyConfig["GENERAL"]['daemonize'] ?? $this->daemonize;
        $this->pidfile                 = $readyConfig["GENERAL"]['pidfile'] ?? $this->pidfile;
        $this->loglevel                = $readyConfig["GENERAL"]['loglevel'] ?? $this->loglevel;
        $this->logfile                 = $readyConfig["GENERAL"]['logfile'] ?? $this->logfile;
        $this->databases               = $readyConfig["GENERAL"]['databases'] ?? $this->databases;
        $this->save                    = $readyConfig["SNAPSHOTTING"]['save'] ?? $this->save;
        $this->stopWritesOnBgsaveError = $readyConfig["SNAPSHOTTING"]['stopWritesOnBgsaveError'] ?? $this->stopWritesOnBgsaveError;
        $this->rdbcompression          = $readyConfig["SNAPSHOTTING"]['rdbcompression'] ?? $this->rdbcompression;
        $this->rdbchecksum             = $readyConfig["SNAPSHOTTING"]['rdbchecksum'] ?? $this->rdbchecksum;
        $this->dbfilename              = $readyConfig["SNAPSHOTTING"]['dbfilename'] ?? $this->dbfilename;
        $this->dir                     = $readyConfig["SNAPSHOTTING"]['dir'] ?? $this->dir;
        $this->renameCommand           = $readyConfig['SECURITY']['renameCommand'] ?? $this->renameCommand;
        $this->requirepass             = $readyConfig['SECURITY']['requirepass'] ?? $this->requirepass;
        $this->maxclients              = $readyConfig['CLIENTS']['maxclients'] ?? $this->maxclients;
        $this->maxmemory               = $readyConfig['MEMORY MANAGEMENT']['maxmemory'] ?? $this->maxmemory;
        $this->maxmemoryPolicy         = $readyConfig['MEMORY MANAGEMENT']['maxmemoryPolicy'] ?? $this->maxmemoryPolicy;
    }

}
