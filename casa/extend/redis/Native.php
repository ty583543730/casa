<?php

/**
 * Created by PhpStorm.
 * User: Bourne
 * DateTime: 2017/5/2 15:37
 */

namespace redis;

class Native
{
    protected static $handler = null;

    public function __construct()
    {
        $redisConfig=config('redis');
        $host = $redisConfig['host'];
        $port = $redisConfig['port'];
        $timeout = $redisConfig['timeout'];
        $password = $redisConfig['auth'];
        $select = $redisConfig['select'];
        if (!extension_loaded('redis')) {   //判断是否有扩展(如果你的apache没reids扩展就会抛出这个异常)
            throw new \BadFunctionCallException('not support: redis');
        }
        self::$handler = new \Redis;
        self::$handler->connect($host, $port, $timeout);
        if ('' != $password) {
            self::$handler->auth($password);
        }
        if (0 != $select) {
            self::$handler->select($select);
        }
    }

    /**
     * 写入缓存
     * @param string $key 键名
     * @param string $value 键值
     * @param int $exprie 过期时间 0:永不过期
     * @return bool
     */
    public static function set($key, $value, $exprie = 0)
    {
        if ($exprie == 0) {
            $set = self::$handler->set($key, $value);
        } else {
            $set = self::$handler->setex($key, $exprie, $value);
        }
        return $set;
    }

    /**
     * 读取缓存
     * @param string $key 键值
     * @return mixed
     */
    public static function get($key)
    {
        $fun = is_array($key) ? 'Mget' : 'get';
        return self::$handler->{$fun}($key);
    }

    public static function rm($key, $key2 = null, $key3 = null)
    {
        return self::$handler->delete($key, $key2, $key3);
    }

    /**
     * 读取缓存
     * @param string $key 键值
     * @return boolean 存在返回 1 ，否则返回 0
     */
    public static function exists($key)
    {
        return self::$handler->exists($key);
    }

    public static function incr($key, $value = 1)
    {
        return self::$handler->incrBy($key, $value);
    }

    //-------------列表操作--------------
    public static function lpush($key, $value)
    {
        return self::$handler->lPush($key, $value);
    }

    public static function rpush($key, $value)
    {
        return self::$handler->rPush($key, $value);
    }

    //移出并获取列表的第一个元素
    public static function lpop($key)
    {
        return self::$handler->lPop($key);
    }

    public static function rpop($key)
    {
        return self::$handler->rPop($key);
    }

    public static function llen($key)
    {
        return self::$handler->lLen($key);
    }

    public static function lindex($key, $index = 0)
    {
        return self::$handler->lIndex($key, $index);
    }

    //hash操作
    public static function hset($key, $hashKey, $value)
    {
        return self::$handler->hset($key, $hashKey, $value);
    }

    public static function hget($key, $hashKey)
    {
        return self::$handler->hget($key, $hashKey);
    }
    public static function hgetall($key)
    {
        return self::$handler->hGetAll($key);
    }


    //-------------集合操作--------------
    public static function sadd($key, $value1, $value2 = null, $valueN = null)
    {
        return self::$handler->sAdd($key, $value1, $value2 = null, $valueN = null);
    }

    public static function srem($key, $value1, $value2 = null, $valueN = null)
    {
        return self::$handler->sRem($key, $value1, $value2 = null, $valueN = null);
    }

    public static function smembers($key)
    {
        return self::$handler->sMembers($key);
    }

    public static function multi($model)
    {
        return self::$handler->multi($model);
    }

    //-------------订阅发布-----------------
    /**
     * @param $channel string 主题
     * @param $message string 消息内容
     * @return int
     */
    public static function publish($channel, $message)
    {
        return self::$handler->publish($channel, $message);
    }
}