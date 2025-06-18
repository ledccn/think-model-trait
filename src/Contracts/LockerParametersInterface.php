<?php

namespace Ledc\ThinkModelTrait\Contracts;

use think\Request;

/**
 * 锁参数接口
 */
interface LockerParametersInterface
{
    /**
     * 生成锁key
     * @param Request $request 请求对象
     * @return string
     */
    public function generateLockingKey(Request $request): string;

    /**
     * 获取锁的过期时间
     * @return int
     */
    public function getExpire(): int;

    /**
     * 是否自动释放锁
     * @return bool
     */
    public function isAutoRelease(): bool;
}
