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

    public function __construct(Config $config): array
    {
        $this->config = $config;
    }
    /**
     * 指定选择的数据库
     * @param int $dbIndex
     * @return \Spool\Pedis\Lib\KeyList
     * @throws PedisException
     */
    public function select(int $dbIndex): KeyList
    {
        if ($dbIndex < 0 || $dbIndex >= $this->config->databases ) {
            throw new PedisException(ErrorCode::getMessage(ErrorCode::CANNOT_SELECT_LARGER_THAN_THE_CONFIGURATION_DATABASE));
        }
        if (!isset($this->db[$dbIndex])) {
            $this->db[$dbIndex] = new KeyList();
        }
        return $this->db[$dbIndex];
    }
    
    public function ping(): string
    {
        return 'PONG';
    }
    public function echos(string $message): string
    {
        return $message;
    }
    public function quit(): string
    {
        return 'OK';
    }
}
