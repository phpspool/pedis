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
use Spool\Pedis\Commands\StringCommand;
use Spool\Pedis\Lib\Log;

/**
 * Description of Commands
 *
 * @author 陈浩波
 */
class Commands
{

    protected $plugin_commands = [];

    /**
     * 数据实例
     * @var DbNode 
     */
    protected $dbNode;

    /**
     * Config
     * @var Config
     */
    protected $config;
    protected $baseCommand = [];

    public function __construct(Config &$config)
    {
        $this->config      = $config;
        $this->dbNode      = new DbNode($config);
        $this->baseCommand = [
            'key'        => '',
            'string'     => new StringCommand(),
            'hash'       => '',
            'list'       => '',
            'set'        => '',
            'zset'       => '',
            'pub'        => '',
            'connection' => new ConnectionCommand(),
        ];
    }

    public function doCommand(string $command, int $clientKey): array
    {
        $cmdInfo = $this->analysisCommand($command);
        if ($cmdInfo['code'] != 1) {
            return $cmdInfo;
        }
        $obj    = $this->baseCommand[$cmdInfo['data']['todo']];
        $cmd    = $cmdInfo['data']['cmd'];
        $key    = $cmdInfo['data']['key'];
        $argcs  = $cmdInfo['data']['argcs'];
        $result = $obj->$cmd($this->dbNode, $clientKey, $key, $argcs);
        ob_start();
        var_dump($this->dbNode);
        $msg    = ob_get_clean();
//        Log::debug($msg);
        return $result;
    }

    /**
     * 解析命令
     * @param string $command
     * @return array
     */
    protected function analysisCommand(string $command): array
    {
        $data = explode(' ', $command);
        if (count($data) < 1) {
            throw new PedisException(ErrorCode::COMMAND_IS_INVALID);
//	    return ['code' => ErrorCode::COMMAND_IS_INVALID, 'msg' => ErrorCode::getMessage(ErrorCode::COMMAND_IS_INVALID)];
        }
        $cmd = strtoupper($data[0]);
        unset($data[0]);
        if (!key_exists($cmd, BASE_COMMANDS)) {
            throw new PedisException(ErrorCode::COMMAND_NOT_FOUND);
//	    return ['code' => ErrorCode::COMMAND_NOT_FOUND, 'msg' => ErrorCode::getMessage(ErrorCode::COMMAND_NOT_FOUND)];
        }
        if (isset($data[1])) {
            $key = $data[1];
            unset($data[1]);
        } else {
            $key = '';
        }
        $todo = BASE_COMMANDS[$cmd];
        array_filter($data);
        return [
            'code' => 1,
            'msg'  => "cmd is: {$cmd}, key is {$todo}",
            'data' => [
                'todo'  => $todo,
                'cmd'   => strtolower($cmd),
                'key'   => $key,
                'argcs' => array_values($data),
            ]
        ];
    }

}
