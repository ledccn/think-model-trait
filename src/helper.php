<?php

namespace Ledc\ThinkModelTrait;

use Ledc\ThinkModelTrait\Contracts\Curl;
use Redis;
use think\cache\Driver;
use think\facade\Cache;

/**
 * 获取redis驱动
 * @return \think\cache\driver\Redis|Driver
 */
function cache_redis_driver(): Driver
{
    return Cache::store('redis');
}

/**
 * 获取redis驱动句柄
 * @return \Predis\Client|Redis
 */
function redis_handler()
{
    return Cache::store('redis')->handler();
}

/**
 * 创建curl对象
 * @return Curl
 */
function make_curl(): Curl
{
    $curl = new Curl();
    $curl->setTimeout()->setSslVerify();
    return $curl;
}