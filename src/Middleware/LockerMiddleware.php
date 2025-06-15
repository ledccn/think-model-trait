<?php

namespace Ledc\ThinkModelTrait\Middleware;

use Closure;
use Ledc\ThinkModelTrait\Contracts\LockerParameters;
use Ledc\ThinkModelTrait\RedisLocker;
use think\Config;
use think\Request;
use think\Response;

/**
 * 限制并发请求的锁中间件
 * - 您可以继承该类，并实现 generateLockingKey 或者 getIdentity 方法，生成锁的KEY
 */
class LockerMiddleware
{
    /**
     * 身份凭据类型：后台管理员
     */
    public const TYPE_ADMIN = 'admin';
    /**
     * 身份凭据类型：客服
     */
    public const TYPE_KEFU = 'kefu';
    /**
     * 身份凭据类型：用户ID
     */
    public const TYPE_UID = 'uid';
    /**
     * 身份凭据类型：IP
     */
    public const TYPE_IP = 'ip';
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
     * @param LockerParameters|null $parameters 中间件传入的锁参数
     * @return Response
     */
    final public function handle(Request $request, Closure $next, ?LockerParameters $parameters = null): Response
    {
        if (is_null($parameters) || $this->config->get('cache.default') === 'file') {
            return $next($request);
        }

        $locker = new RedisLocker(
            $this->generateLockingKey($request, $parameters),
            $parameters->expire,
            $parameters->autoRelease
        );
        if (!$locker->acquire()) {
            return Response::create($this->body, 'json', $this->httpStatus);
        }

        return $next($request);
    }

    /**
     * 生成锁key
     * - 默认 Method + URI 作为锁KEY
     * @param Request $request 请求对象
     * @param LockerParameters $parameters 锁参数
     * @return string
     */
    protected function generateLockingKey(Request $request, LockerParameters $parameters): string
    {
        $type = $parameters->type;
        $identity = $this->getIdentity($request, $type);
        $keys = [
            'locker',
            $type,
            $identity,
            sha1(implode(':', [
                // 当前文件路径，防止缓存冲突
                __FILE__,
                // 当前请求类型
                $request->method(true),
                // 当前请求 URI
                $request->rule()->getRule(),
            ]))
        ];
        return implode(':', $keys);
    }

    /**
     * 获取请求者身份凭据
     * @param Request $request 请求对象
     * @param string $type 请求者身份凭据类型
     * @return string
     */
    protected function getIdentity(Request $request, string $type): string
    {
        switch ($type) {
            case self::TYPE_ADMIN:
                return $request->adminId();
            case self::TYPE_KEFU;
                return $request->kefuId();
            case self::TYPE_UID:
                return $request->uid();
            case self::TYPE_IP:
            default:
                return $request->ip();
        }
    }
}
