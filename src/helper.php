<?php

namespace Ledc\ThinkModelTrait;

use Ledc\ThinkModelTrait\Contracts\Curl;
use Redis;
use RuntimeException;
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

/**
 * 使用curl下载远程文件
 * @param string $url
 * @param string $encoding
 * @return string
 */
function curl_get_remote_file(string $url, string $encoding = 'gzip,deflate'): string
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_ENCODING, $encoding);
    curl_setopt($ch, CURLOPT_HEADER, false);
    if (parse_url($url, PHP_URL_SCHEME) === 'https') {
        //false 禁止 cURL 验证对等证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //0 时不检查名称（SSL 对等证书中的公用名称字段或主题备用名称（Subject Alternate Name，简称 SNA）字段是否与提供的主机名匹配）
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // 自动跳转，跟随请求Location
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2);         // 递归次数
    $contents = curl_exec($ch);
    $httpStatusCode = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $curlErrorNo = curl_errno($ch);
    $curlErrorMessage = curl_error($ch);
    curl_close($ch);
    // 判断请求结果
    if (is_string($contents) && 200 <= $httpStatusCode && $httpStatusCode < 300) {
        return $contents;
    }
    throw new RuntimeException("CURL Error: $curlErrorMessage ($curlErrorNo), httpStatusCode $httpStatusCode" . ($contents ? ', body ' . $contents : ''));
}
