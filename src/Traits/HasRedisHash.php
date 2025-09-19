<?php

namespace Ledc\ThinkModelTrait\Traits;

use Ledc\ThinkModelTrait\RedisUtils;
use Redis;

/**
 * Redis哈希表
 */
trait HasRedisHash
{
    /**
     * 完整的哈希表键名
     * @var string
     */
    protected string $keyName;

    /**
     * 【获取】完整的哈希表键名
     * @return string
     */
    public function getKeyName(): string
    {
        return $this->keyName;
    }

    /**
     * 【设置】完整的哈希表键名
     * @param string $key_name
     * @return HasRedisHash
     */
    public function setKeyName(string $key_name): self
    {
        $this->keyName = $key_name;
        return $this;
    }

    /**
     * 获取存储在哈希表中指定字段的值
     * - 原生
     * @param string $field
     * @return false|mixed
     */
    public function hGet(string $field)
    {
        return static::connection()->hGet($this->getKeyName(), $field);
    }

    /**
     * 将哈希表中的字段 field 的值设为 value
     * - 原生
     * @param string $field 字段名称
     * @param string $value 字段值
     * @return false|int
     */
    public function hSet(string $field, string $value)
    {
        return static::connection()->hSet($this->getKeyName(), $field, $value);
    }

    /**
     * 获取存储在哈希表中指定字段的值
     * - json_decode解码
     * @param string $field 字段名称
     * @return array|string|int|float|bool|null
     */
    public function get(string $field)
    {
        $value = static::connection()->hGet($this->getKeyName(), $field);
        return is_null($value) ? null : json_decode($value, true);
    }

    /**
     * 将哈希表中的字段 field 的值设为 value
     * - json_encode编码
     * @param string $field 字段名称
     * @param array|string|int|float|bool $value 字段值
     * @return false|int
     */
    public function set(string $field, $value)
    {
        return static::connection()->hSet($this->getKeyName(), $field, json_encode($value, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 判断哈希表的指定字段是否存在
     * @param string $field 字段名称
     * @return bool
     */
    public function has(string $field): bool
    {
        return static::connection()->hExists($this->getKeyName(), $field);
    }

    /**
     * 删除哈希表字段
     * @param string $field 字段名称
     * @return false|int
     */
    public function del(string $field)
    {
        return static::connection()->hDel($this->getKeyName(), $field);
    }

    /**
     * 获取Redis连接
     * @return Redis|\Predis\Client|object
     */
    public static function connection(): Redis
    {
        return RedisUtils::handler();
    }
}
