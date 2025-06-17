<?php

namespace Ledc\ThinkModelTrait\Contracts;

use think\Request;

/**
 * 锁参数类
 * - 适用于 CRMEB单商户、CRMEB多门店
 */
class LockerParameters extends AbstractLockerParameters
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
     * 身份凭据类型：门店
     */
    public const TYPE_STORE = 'store';
    /**
     * 身份凭据类型：供应商
     */
    public const TYPE_SUPPLIER = 'supplier';
    /**
     * 身份凭据类型：收银员
     */
    public const TYPE_CASHIER = 'cashier';
    /**
     * 身份凭据类型：IP
     */
    public const TYPE_IP = 'ip';

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
     * 构造门店锁参数
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     * @return self
     */
    public static function builderStore(int $expire = 10, bool $autoRelease = true): self
    {
        return self::make(self::TYPE_STORE, $expire, $autoRelease);
    }

    /**
     * 构造供应商锁参数
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     * @return self
     */
    public static function builderSupplier(int $expire = 10, bool $autoRelease = true): self
    {
        return self::make(self::TYPE_SUPPLIER, $expire, $autoRelease);
    }

    /**
     * 构造收银员锁参数
     * @param int $expire 锁的过期时间，单位：秒
     * @param bool $autoRelease 是否自动释放锁
     * @return self
     */
    public static function builderCashier(int $expire = 10, bool $autoRelease = true): self
    {
        return self::make(self::TYPE_CASHIER, $expire, $autoRelease);
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
            $this->getType(),
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
        switch ($this->getType()) {
            case self::TYPE_ADMIN:
                return $request->adminId();
            case self::TYPE_KEFU;
                return $request->kefuId();
            case self::TYPE_UID:
                return $request->uid();
            case self::TYPE_STORE:
                return $request->storeId();
            case self::TYPE_SUPPLIER:
                return $request->supplierId();
            case self::TYPE_CASHIER:
                return $request->cashierId();
            case self::TYPE_IP:
            default:
                return $request->ip();
        }
    }
}
