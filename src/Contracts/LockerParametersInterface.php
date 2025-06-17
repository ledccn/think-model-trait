<?php

namespace Ledc\ThinkModelTrait\Contracts;

use think\Request;

/**
 * 锁参数接口
 * - 您可以继承该类，并实现 generateLockingKey 方法，生成锁的KEY
 */
abstract class LockerParametersInterface
{
    /**
     * 锁的身份凭据类型(枚举值)
     * @var string
     */
    private string $type;
    /**
     * 锁的过期时间，单位：秒
     * @var int
     */
    private int $expire;
    /**
     * 是否自动释放锁
     * @var bool
     */
    private bool $autoRelease;

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
     * 生成锁key
     * @param Request $request 请求对象
     * @return string
     */
    abstract public function generateLockingKey(Request $request): string;

    /**
     * 获取锁身份凭据类型
     * @return string
     */
    final public function getType(): string
    {
        return $this->type;
    }

    /**
     * 获取锁的过期时间
     * @return int
     */
    final public function getExpire(): int
    {
        return $this->expire;
    }

    /**
     * 是否自动释放锁
     * @return bool
     */
    final public function isAutoRelease(): bool
    {
        return $this->autoRelease;
    }
}