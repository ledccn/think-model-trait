<?php

namespace Ledc\ThinkModelTrait;

use Closure;
use Exception;
use think\facade\Cache;

/**
 * Redis 锁
 */
class RedisLocker
{
    /**
     * 锁的键名
     * @var string
     */
    protected string $key;
    /**
     * 锁的过期时间，单位：秒
     * @var int
     */
    protected int $expire = 30;
    /**
     * 锁的唯一标识（用于解锁）
     * @var string
     */
    protected string $identifier;
    /**
     * 是否自动释放锁
     * @var bool
     */
    protected bool $autoRelease = true;

    /**
     * 构造函数
     * @param string $lockKey
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease
     */
    public function __construct(string $lockKey, int $expire = 30, bool $autoRelease = true)
    {
        $this->key = 'lock:' . md5(__FILE__) . ':' . $lockKey;
        $this->expire = $expire;
        $this->autoRelease = $autoRelease;
        // 唯一标识本次锁
        $this->identifier = uniqid();
    }

    /**
     * 尝试加锁
     * - 锁成功返回 true，锁失败返回 false
     * @return bool
     */
    public function lock(): bool
    {
        return $this->acquire();
    }

    /**
     * 尝试加锁
     * - 锁成功返回 true，锁失败返回 false
     * @return bool
     */
    final public function acquire(): bool
    {
        try {
            // SET key value NX EX=不存在时设置并设置过期时间
            $result = self::handler()->set($this->key, $this->identifier, ['NX', 'EX' => $this->expire]);
            return $result !== false;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * 释放锁
     * @return bool
     */
    final public function release(): bool
    {
        try {
            // 使用 Lua 脚本确保原子性：只有当前持有者可以删除锁
            $script = <<<LUA
if redis.call("get", KEYS[1]) == ARGV[1] then
    return redis.call("del", KEYS[1])
else
    return 0
end
LUA;

            $result = self::handler()->eval($script, [$this->key, $this->identifier], 1);
            return $result === 1;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * 获取redis句柄
     * @return \Predis\Client|\Redis
     */
    final protected static function handler()
    {
        return Cache::store('redis')->handler();
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return static
     */
    public static function __callStatic(string $method, array $arguments)
    {
        $class = str_replace('::', '_', static::class);
        $key = $arguments[0] ?? 'empty';
        unset($arguments[0]);
        $arguments = array_values($arguments);
        return new RedisLocker($class . ':' . $method . ':' . $key, ...$arguments);
    }

    /**
     * 加锁并执行闭包
     * @param string $lockKey 锁KEY
     * @param callable|Closure $fn 闭包
     * @param int $expire 锁过期时间
     * @return mixed
     */
    final public static function exec(string $lockKey, callable $fn, int $expire = 30)
    {
        $locker = new RedisLocker($lockKey, $expire, true);
        $locker->acquire();
        return $fn($locker);
    }

    /**
     * 析构函数
     * - 自动释放锁
     */
    public function __destruct()
    {
        if ($this->autoRelease) {
            $this->release();
        }
    }
}
