<?php

namespace Ledc\ThinkModelTrait\Middleware;

use Closure;
use Ledc\ThinkModelTrait\Contracts\AbstractLockerParameters;
use Ledc\ThinkModelTrait\RedisLocker;
use think\Config;
use think\Request;
use think\Response;

/**
 * 限制并发请求的锁中间件
 */
class LockerMiddleware
{
    /**
     * 未获取到锁的 HTTP 状态码
     * @var int
     */
    protected int $httpStatus = 200;
    /**
     * 未获取到锁的响应数据
     * @var array|string[]
     */
    protected array $body = [
        'status' => 429,
        'msg' => '请求太频繁，未获取到锁',
    ];
    /**
     * 配置管理类
     * @var Config
     */
    protected Config $config;

    /**
     * 构造方法
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        // 触发限流后的响应数据
        $this->body = $config->get('locker.body') ?: $this->body;
        // 触发限流后的 HTTP 状态码
        $this->httpStatus = $config->get('locker.http_status') ?: $this->httpStatus;
    }

    /**
     * 处理请求
     * @param Request $request 请求对象
     * @param Closure $next 闭包
     * @param AbstractLockerParameters|null $parameters 中间件传入的锁参数
     * @return Response
     */
    final public function handle(Request $request, Closure $next, ?AbstractLockerParameters $parameters = null): Response
    {
        if (is_null($parameters) || $this->config->get('cache.default') === 'file') {
            return $next($request);
        }

        $locker = new RedisLocker(
            $parameters->generateLockingKey($request),
            $parameters->getExpire(),
            $parameters->isAutoRelease()
        );
        if (!$locker->acquire()) {
            return Response::create($this->body, 'json', $this->httpStatus);
        }

        return $next($request);
    }
}
