<?php

namespace Ledc\ThinkModelTrait\Contracts;

use think\Request;

/**
 * 锁参数类
 */
class LockerParameters
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
        return self::make(self::TYPE_ADMIN, $expire, $autoRelease);
    }

    /**
     * 构造客服锁参数
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     * @return self
     */
    public static function builderKefu(int $expire = 10, bool $autoRelease = true): self
    {
        return self::make(self::TYPE_KEFU, $expire, $autoRelease);
    }

    /**
     * 构造用户UID锁参数
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     * @return self
     */
    public static function builderUid(int $expire = 10, bool $autoRelease = true): self
    {
        return self::make(self::TYPE_UID, $expire, $autoRelease);
    }

    /**
     * 构造IP锁参数
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     * @return self
     */
    public static function builderIP(int $expire = 10, bool $autoRelease = true): self
    {
        return self::make(self::TYPE_IP, $expire, $autoRelease);
    }

    /**
     * 生成锁key
     * - 默认 Method + URI 作为锁KEY
     * @param Request $request 请求对象
     * @return string
     */
    public function generateLockingKey(Request $request): string
    {
        $identity = $this->getIdentity($request);
        $keys = [
            'locker',
            $this->type,
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
     * @return string
     */
    protected function getIdentity(Request $request): string
    {
        switch ($this->type) {
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
