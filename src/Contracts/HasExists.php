<?php

namespace Ledc\ThinkModelTrait\Contracts;

/**
 * 【内部参数】标识是否存在
 */
trait HasExists
{
    /**
     * 【内部参数】是否存在
     * @var bool
     */
    private bool $exists = false;

    /**
     * 设置是否存在
     * @param bool $exists
     * @return $this
     */
    public function setExists(bool $exists = true): self
    {
        $this->exists = $exists;
        return $this;
    }

    /**
     * 判断是否存在
     * @return bool
     */
    public function isExists(): bool
    {
        return $this->exists;
    }
}