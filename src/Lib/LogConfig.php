<?php

declare(strict_types = 1);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Lib;

/**
 * 独立的日志配置文件
 *
 * @author 大天使长
 */
class LogConfig
{
    /**
     * 日志存储介质的切换选型。0:STDOUT 1:File 2:TCP 3:UDP (默认为0)
     * @var int
     */
    public $appender = 0;
    /**
     * 记录日志时的重试次数。 默认为 0 (不重试)
     * @var int
     */
    public $appenderRetry = 0;
    /**
     * 日志buffer大小， 默认0，不开启
     * @var int
     */
    public $bufferSize = 0;
    /**
     * 日志存储的默认根路径
     * @var string
     */
    public $defaultBasepath = PEDIS_ROOT . '/runtime/log';
    /**
     * 时间格式，默认Y-m-d H:i:s
     * @var string
     */
    public $defaultDatetimeFormat = "Y-m-d H:i:s";
    /**
     * 日志记录的默认 Logger。默认值为 "default"。
     * @var string
     */
    public $defaultLogger = 'default';
    /**
     * 日志记录的默认 Logger。默认值为 "default"。
     * @var string
     */
    public $defaultExt = 'log';
    /**
     * 是否按每小时一个记录进行区分。1-Y 0-N(默认值)
     * @var int
     */
    public $distingByHour = 0;
    /**
     * 是否按目录进行区分。1-Y(默认值) 0-N
     * @var int
     */
    public $distingFolder = 0;
    /**
     * 是否按日志级别进行区分。1-Y 0-N(默认值)
     * @var int
     */
    public $distingType = 0;
    /**
     * 是否忽略警告。1-On(默认值) 0-Off
     * @var int
     */
    public $ignoreWarning = 0;
    /**
     * 允许日志被记录的级别。默认为 8 (全部日志)。 
     * 0-EMERGENCY 1-ALERT 2-CRITICAL 3-ERROR 4-WARNING 5-NOTICE 6-INFO 7-DEBUG 8-ALL
     * @var int
     */
    public $level = 8;
    /**
     * 如果要使用 TCP 或者 UDP 为存储介质，需要配置远端的 IP。默认值为 "127.0.0.1"
     * @var string
     */
    public $remoteHost = '127.0.0.1';
    /**
     * 如果要使用 TCP 或者 UDP 为存储介质，需要配置远端服务的端口号。默认值为 514
     * @var int
     */
    public $remotePort = 514;
    /**
     * 如果要使用 TCP 或者 UDP 为存储介质，需要配置超时时间。默认值为 1 秒。
     * @var int
     */
    public $remoteTimeout = 1;
    /**
     * 是否接受Log抛出异常。1-On(默认值) 0-Off
     * @var int
     */
    public $throwException = 1;
    /**
     * 自动地 Trim 掉日志信息中的 \n 和 \r。1-On 0-Off(默认值)
     * @var int
     */
    public $trimWrap = 0;
    /**
     * 开启使用内存中的日志 Buffer。1-Y 0-N(默认值)
     * @var int
     */
    public $useBuffer = 0;
    /**
     * 默认日志模板。 默认值是 "%T | %L | %P | %Q | %t | %M".
     * 更多格式请查看seaslog文档
     * https://www.php.net/manual/zh/seaslog.configuration.php
     * @var string
     */
    public $defaultTemplate = "%T | %L | %P | %Q | %t | %M";
}
