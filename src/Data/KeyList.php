<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Data;

use Spool\Pedis\Exceptions\PedisException;
use Spool\Pedis\Constants\ErrorCode;

/**
 * Pedis的键列表类,用于操作数据库里面的所有键值
 *
 * @author 陈浩波
 */
class KeyList
{

    public static $toSearch = '';

    /**
     * 实际储存的数据
     * @var array[KeyNode]
     */
    private $data = [];

    /**
     * 当前数据库中保存的所有键值
     * @var array
     */
    private $keys = [];

    /**
     * 对键值变更设置回调函数
     * @var array
     */
    private $changWatch = [];

    /**
     * 对键是否存在设置回调函数
     * @var array
     */
    private $existsWatch = [];

    /**
     * lru算法内存超限时清理
     * @var int
     */
    private $lru = [];

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
            if (isset($this->data[$keyName])) {
                unset($this->data[$keyName]);
                $index = array_search($keyName, $this->keys);
                if (FALSE != $index) {
                    unset($this->keys[$index]);
                }
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
        /**
         * 使用isset而不是array_key_exists的原因是,
         * isset的性能要比array_key_exists快很多,特别是大数量的时候
         * 不用empty是因为值为0时会返回false,但是值为0的时候在缓存里面情况很正常.
         * pedis不追求速度,但是不能不考虑速度
         */
        return isset($this->data[$key]) ? 1 : 0;
    }

    public function expire(string $key, int $seconds): int
    {
        if (!isset($this->data[$key])) {
            return 0;
        }
        $keyNode = $this->getKey($key);
        return $keyNode->expire($seconds);
    }

    public function expireAt(string $key, int $seconds): int
    {
        if (!isset($this->data[$key])) {
            return 0;
        }
        $keyNode = $this->getKey($key);
        return $keyNode->expireAt($seconds);
    }

    /**
     * 搜索pedis的key,初期设计的并不复杂,只实现了*|?|=,通配符只能有一个,以后再优化
     * 这个方法必然是速度很慢的
     * @param string $key
     * @return array
     */
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
                    if (FALSE != stripos($item, '*')) {
                        $search = explode('*', $item);
                        if ($search[0] === mb_substr($item, 0, strlen($search[0])) && $search[1] === mb_substr($item, 0 - strlen($search[1]))) {
                            $data[] = $item;
                        }
                    }
                    if (FALSE != stripos($item, '?')) {
                        $search = explode('*', $item);
                        if ($search[0] === mb_substr($item, 0, strlen($search[0])) && $search[1] === mb_substr($item, 0 - strlen($search[1]))) {
                            $data[] = $item;
                        }
                    }
                }, $data);
                self::$toSearch = '';
                break;
        }
        return $data;
    }

    /**
     * 移除该健之前设定的生存时间
     * @param string $key
     */
    public function persist(string $key): int
    {
        if (!isset($this->data[$key])) {
            return 0;
        }
        $keyNode = $this->getKey($key);
        return $keyNode->persist();
    }

    /**
     * 根据键名,返回指定键的类
     * @param string $key
     * @return \Spool\Pedis\Lib\KeyNode
     * @throws PedisException
     */
    public function getStringKey(string $key): StringKeyNode
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = new StringKeyNode();
            return $this->data[$key];
        } else if ($this->data[$key] instanceof StringKeyNode) {
            return $this->data[$key];
        } else {
            throw new PedisException(ErrorCode::getMessage(ErrorCode::DATA_FORMATTING_ERROR));
        }
    }

}
