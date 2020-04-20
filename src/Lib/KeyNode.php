<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Lib;

/**
 * Description of CacheNode
 *
 * @author 陈浩波
 */
abstract class KeyNode
{
    /**
     * 该键实际保存的数据
     * @var void
     */
    private $data;
    /**
     * 生存时间
     * @var int -1永不过期|0已过期|剩余的生存时间
     */
    private $expire = -1;
    /**
     * 设置生存时间的时间
     * @var int 
     */
    private $expireTime = 0;
    /**
     * 键的类型
     * @var string String|List|Set|Zset|Hash
     */
    private $type = 'String';
    /**
     * lru算法内存超限时清理
     * @var int
     */
    private $lru = -1;

    public function __sleep (): array
    {
        return ['data' => $this->data, 'expire' => $this->expire, 'type' => $this->type];
    }
    
    public function expire(int $seconds): int
    {
        $time = time();
        $this->expireTime = $time;
        $this->expire = $seconds;
        return 1;
    }
    public function expireAt(int $seconds): int
    {
        $time = time();
        //如果设置的时间小于当前时间,返回0
        if ($seconds < $time) {
            return 0;
        }
        $this->expireTime = $time;
        $this->expire = $seconds - $time;
        return 1;
    }
}
