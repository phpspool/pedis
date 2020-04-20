<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Lib;

use Spool\Pedis\Lib\KeyNode;

/**
 * Pedis的数据库类,用于操作数据库里面的所有键值
 *
 * @author 陈浩波
 */
class DbNode
{

    public static $toSearch = '';

    /**
     * 实际储存的数据
     * @var array[KeyNode]
     */
    private $data           = [];

    /**
     * 当前数据库中保存的所有键值
     * @var array
     */
    private $keys           = [];

    /**
     * 翻转的当前所有的键值
     * @var array
     */
    private $flipKeys       = [];

    /**
     * 
     */
    public function __construct()
    {
        
    }

    /**
     * 批量删除多个key
     * @param array $key
     * @return int 删除成功的数量
     */
    public function del(array $key): int
    {
        $i = 0;
        foreach ($key as $keyName) {
            if (key_exists($keyName, $this->data)) {
                unset($this->data[$keyName]);
                $index = $this->flipKeys[$keyName];
                unset($this->keys[$index]);
                unset($this->flipKeys[$keyName]);
                $i++;
            }
        }
        return $i;
    }

    /**
     * 判断一个key是否存在
     * @param string $key
     * @return int
     */
    public function exists(string $key): int
    {
        return array_key_exists($key, $this->data) ? 1 : 0;
    }

    public function expire(string $key, int $seconds): int
    {
        if (!key_exists($key, $this->data)) {
            return 0;
        }
        /** @var KeyNode $keyNode */
        $keyNode = &$this->data[$key];
        return $keyNode->expire($seconds);
    }

    public function expireAt(string $key, int $seconds): int
    {
        if (!key_exists($key, $this->data)) {
            return 0;
        }
        /** @var KeyNode $keyNode */
        $keyNode = &$this->data[$key];
        return $keyNode->expireAt($seconds);
    }

    public function keys(string $key): array
    {
        $data = [];
        switch ($key) {
            case '*':
                $data           = $this->keys;
                break;
            case '':
                break;
            default:
                self::$toSearch = $key;
                array_walk($this->keys, function($item, string $key, array &$data) {
                    if (true) {
                        
                    }
                }, $data);
                self::$toSearch = '';
                break;
        }
        return $data;
    }

}
