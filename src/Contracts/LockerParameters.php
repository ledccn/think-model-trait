<?php

namespace Ledc\ThinkModelTrait\Contracts;

use Ledc\ThinkModelTrait\Middleware\LockerMiddleware;

/**
 * 锁参数类
 */
class LockerParameters
{
    /**
     * 锁的身份凭据类型(枚举值)
     * @var string
     */
    public string $type = 'uid';
    /**
     * 锁的过期时间，单位：秒
     * @var int
     */
    public int $expire = 10;
    /**
     * 是否自动释放锁
     * @var bool
     */
    public bool $autoRelease = true;

    /**
     * 构造函数
     * @param string $type 锁的身份凭据类型
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     */
    final protected function __construct(string $type, int $expire, bool $autoRelease = true)
    {
        $this->type = $type;
        $this->expire = $expire;
        $this->autoRelease = $autoRelease;
    }

    /**
     * 创建锁参数
     * @param string $type
     * @param int $expire
     * @param bool $autoRelease
     * @return self
     */
    final public static function make(string $type, int $expire, bool $autoRelease = true): self
    {
        return new static($type, $expire, $autoRelease);
    }

    /**
     * 构造后台管理员锁参数
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     * @return self
     */
    public static function builderAdmin(int $expire = 10, bool $autoRelease = true): self
    {
        return self::make(LockerMiddleware::TYPE_ADMIN, $expire, $autoRelease);
    }

    /**
     * 构造客服锁参数
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     * @return self
     */
    public static function builderKefu(int $expire = 10, bool $autoRelease = true): self
    {
        return self::make(LockerMiddleware::TYPE_KEFU, $expire, $autoRelease);
    }

    /**
     * 构造用户UID锁参数
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     * @return self
     */
    public static function builderUid(int $expire = 10, bool $autoRelease = true): self
    {
        return self::make(LockerMiddleware::TYPE_UID, $expire, $autoRelease);
    }

    /**
     * 构造IP锁参数
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     * @return self
     */
    public static function builderIP(int $expire = 10, bool $autoRelease = true): self
    {
        return self::make(LockerMiddleware::TYPE_IP, $expire, $autoRelease);
    }
}
