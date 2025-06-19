<?php

namespace Ledc\ThinkModelTrait\Enums;

use Ledc\ThinkModelTrait\Contracts\EnumsInterface;

/**
 * 启用禁用枚举
 */
class EnabledEnums extends EnumsInterface
{
    /**
     * 启用
     */
    public const YES = 1;
    /**
     * 禁用
     */
    public const NO = 0;

    /**
     * 枚举说明列表
     * @return string[]
     */
    public static function cases(): array
    {
        return [
            self::YES => '是',
            self::NO => '否',
        ];
    }
}
