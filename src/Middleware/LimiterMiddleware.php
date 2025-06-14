<?php

namespace Ledc\ThinkModelTrait\Middleware;

use Closure;
use Ledc\ThinkModelTrait\RedisUtils;
use RedisException;
use think\Config;
use think\Request;
use think\Response;

/**
 * IP限流中间件
 * - 根据IP限制请求频率
 */
class LimiterMiddleware
{
    /**
     * 默认限流规则
     */
    public const DEFAULT_RULE = [
        'limit' => 5,   // 允许的请求数
        'window' => 3,  // 限流时间窗口（单位秒）
    ];
    /**
     * 限流规则
     * @var array|int[]
     */
    protected array $rule = self::DEFAULT_RULE;
    /**
     * 触发限流后的 HTTP 状态码
     * @var int
     */
    protected int $httpStatus = 429;
    /**
     * 触发限流后的响应数据
     * @var array|string[]
     */
    protected array $body = [
        'code' => 429,
        'msg' => 'Too Many Requests',
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
        // 限流规则
        $this->rule = $config->get('limiter.rule') ?: self::DEFAULT_RULE;
        // 触发限流后的响应数据
        $this->body = $config->get('limiter.body') ?: $this->body;
        // 触发限流后的 HTTP 状态码
        $this->httpStatus = $config->get('limiter.http_status') ?: $this->httpStatus;
    }

    /**
     * 处理请求
     * @param Request $request 请求对象
     * @param Closure $next 闭包
     * @param array $rule 限流规则
     * @return Response
     * @throws RedisException
     */
    public function handle(Request $request, Closure $next, array $rule = []): Response
    {
        if ($this->config->get('cache.default') === 'file') {
            return $next($request);
        }

        $rule = empty($rule) ? $this->rule : array_merge($this->rule, $rule);
        $script = <<<LUA
local current = redis.call('incr', KEYS[1])
if current == 1 then
    redis.call('expire', KEYS[1], ARGV[1])
end
if current > tonumber(ARGV[2]) then
    return 0
else
    return current
end
LUA;

        $redis = RedisUtils::handler();
        $identifier = $this->generateRateLimitingKey($request);
        $result = $redis->eval($script, [$identifier, $rule['window'], $rule['limit']], 1);
        if (0 === (int)$result) {
            $header = [
                'X-RateLimit-Limit' => $rule['limit'],
                'X-RateLimit-Remaining' => 0,
                'X-RateLimit-Reset' => (int)($redis->ttl($identifier) ?: 0),
            ];
            return Response::create($this->body, 'json', $this->httpStatus)->header($header);
        }

        return $next($request);
    }

    /**
     * 生成限流key
     * - 默认 IP + Method + URI + RuleMethod 作为限流KEY
     * @param Request $request 请求对象
     * @return string
     */
    protected function generateRateLimitingKey(Request $request): string
    {
        return 'limiter:' . sha1(implode(':', [
                // 当前文件路径，防止缓存冲突
                __FILE__,
                // 访问者 IP
                $request->ip(),
                // 当前请求类型
                $request->method(true),
                // 当前请求 URI
                $request->rule()->getRule(),
                // 当前路由定义的请求类型
                $request->rule()->getMethod(),
            ]));
    }
}
