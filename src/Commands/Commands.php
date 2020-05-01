<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Commands;

use Spool\Pedis\Lib\Config;
use Spool\Pedis\Data\DbNode;
use Spool\Pedis\Exceptions\PedisException;
use Spool\Pedis\Constants\ErrorCode;
use Spool\Pedis\Commands\ConnectionCommand;


/**
 * Description of Commands
 *
 * @author 陈浩波
 */
class Commands
{

    const BASE_COMMANDS    = [
        'key'        => '',
        'string'     => '',
        'hash'       => '',
        'list'       => '',
        'set'        => '',
        'zset'       => '',
        'pub'        => '',
        'connection' => 'ConnectionCommand',
        'server'     => 'ServerCommand'
    ];
    const PLUGING_COMMANDS = [];
    
    /**
     * 数据实例
     * @var DbNode 
     */
    private $dbNode;
    /**
     * Config
     * @var Config
     */
    private $config;
    private $baseCommand = [];
    public function __construct(Config &$config)
    {
        $this->config = $config;
        $this->dbNode = new DbNode($config);
	$this->baseCommand = [
	    'connection' => new ConnectionCommand(),
	];
    }
    public function doCommand(string $command, int $clientKey): array
    {
        return $this->analysisCommand($command);
    }
    /**
     * 解析命令
     * @param string $command
     * @return array
     */
    private function analysisCommand(string $command): array
    {
	$data = explode(' ', $command);
	if (count($data) < 1) {
	    throw new PedisException(ErrorCode::COMMAND_IS_INVALID);
//	    return ['code' => ErrorCode::COMMAND_IS_INVALID, 'msg' => ErrorCode::getMessage(ErrorCode::COMMAND_IS_INVALID)];
	}
	$cmd = strtoupper($data[0]);
	if (!key_exists($cmd, BASE_COMMANDS)) {
	    throw new PedisException(ErrorCode::COMMAND_NOT_FOUND);
//	    return ['code' => ErrorCode::COMMAND_NOT_FOUND, 'msg' => ErrorCode::getMessage(ErrorCode::COMMAND_NOT_FOUND)];
	}
	$todo = BASE_COMMANDS[$cmd];
	return [
	    'code' => 1,
	    'msg' => "cmd is: {$cmd}, key is {$todo}",
	];
    }
}
