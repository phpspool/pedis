<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Data;

use Spool\Pedis\Exceptions\PedisException;
use Spool\Pedis\Constants\ErrorCode;
use Spool\Pedis\Lib\Config;
use Spool\Pedis\Lib\Log;

/**
 * Pedis的数据库类,用于操作数据库的相关操作
 *
 * @author 陈浩波
 */
class DbNode
{
    /**
     * 数据库列表
     * @var array
     */
    private $db = [];
    /**
     * 配置信息
     * @var Config 
     */
    private $config;
    
    private $dbIndex = [];
    
    public function __construct(Config &$config)
    {
        $this->config = $config;
        $this->db[0] = new KeyList();
    }
    /**
     * 指定选择的数据库
     * 这里的实现不对,应该是每个客户端都能选择自己要操作的数据库,而不是一次选择,都得操作一个库
     * @param int $clientKey 客户端
     * @param int $dbIndex
     * @return \Spool\Pedis\Lib\KeyList
     * @throws PedisException
     */
    public function select(int $clientKey, int $dbIndex): array
    {
        if ($dbIndex < 0 || $dbIndex >= $this->config->databases ) {
            throw new PedisException(ErrorCode::getMessage(ErrorCode::CANNOT_SELECT_LARGER_THAN_THE_CONFIGURATION_DATABASE));
        }
        if (!isset($this->db[$dbIndex])) {
            $this->db[$dbIndex] = new KeyList();
        }
        $this->dbIndex[$clientKey] = $dbIndex;
        $result = [
            'code' => 0,
            'msg' => 'OK'
        ];
        return $result;
    }
    
    public function getClientDb(int $clientKey): KeyList
    {
	$dbIndex = isset($this->dbIndex[$clientKey]) ? $this->dbIndex[$clientKey] : 0;
	return $this->db[$dbIndex];
    }
    
    public function ping(): array
    {
        $result = [
            'code' => 0,
            'msg' => 'PONG'
        ];
        return $result;
    }
    public function echos(string $message): array
    {
        $result = [
            'code' => 0,
            'msg' => $message,
        ];
        return $result;
    }
    public function quit(): array
    {
        $result = [
            'code' => 0,
            'msg' => 'OK'
        ];
        return $result;
    }
}
