<?php

namespace Ledc\ThinkModelTrait\Traits;

/**
 * 序列化和反序列化
 */
trait HasSerialization
{
    /**
     * 是否序列化和反序列化
     */
    protected static function isSerialization(): bool
    {
        return true;
    }

    /**
     * 序列化
     * @param mixed $value
     * @return string
     */
    protected static function serialize($value): string
    {
        if (!static::isSerialization()) {
            return $value;
        }
        return serialize($value);
    }

    /**
     * 反序列化
     * @param string $value
     * @return mixed
     */
    protected static function unserialize(string $value)
    {
        if (!static::isSerialization()) {
            return $value;
        }
        return unserialize($value);
    }
}
