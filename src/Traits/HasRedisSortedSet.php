<?php

namespace Ledc\ThinkModelTrait\Traits;

use Ledc\ThinkModelTrait\RedisUtils;
use Redis;

/**
 * Redis有序集合
 */
trait HasRedisSortedSet
{
    /**
     * 有序集合的key
     * @var string
     */
    protected string $sortedSetKey;

    /**
     * 【获取】有序集合的key
     * @return string
     */
    public function getSortedSetKey(): string
    {
        return $this->sortedSetKey;
    }

    /**
     * 【设置】有序集合的key
     * @param string $sortedSetKey
     * @return HasRedisSortedSet
     */
    public function setSortedSetKey(string $sortedSetKey): self
    {
        $this->sortedSetKey = $sortedSetKey;
        return $this;
    }

    /**
     * 向有序集合添加一个或多个成员，或者更新已存在成员的分数
     * @param int|string $score int或double的分数
     * @param string $member
     * @return false|float|int
     */
    public function zAdd($score, string $member)
    {
        return static::connection()->zAdd($this->getSortedSetKey(), $score, $member);
    }

    /**
     * 有序集合中对指定成员的分数加上增量 increment
     * @param int|string $value
     * @param string $member
     * @return false|float
     */
    public function zIncrBy($value, string $member)
    {
        return static::connection()->zIncrBy($this->getSortedSetKey(), $value, $member);
    }

    /**
     * 移除有序集合中的一个或多个成员
     * @param string $member
     * @return false|int
     */
    public function zRem(string $member)
    {
        return static::connection()->zRem($this->getSortedSetKey(), $member);
    }

    /**
     * 获取有序集合的成员数
     * @return false|int
     */
    public function zCard()
    {
        return static::connection()->zCard($this->getSortedSetKey());
    }

    /**
     * 返回有序集合中指定成员的索引(索引从0开始)
     * @param string $member
     * @return int|false 不存在返回false
     */
    public function zRank(string $member)
    {
        return static::connection()->zRank($this->getSortedSetKey(), $member);
    }

    /**
     * 返回有序集中，成员的分数值
     * @param string $member
     * @return false|float 不存在返回false
     */
    public function zScore(string $member)
    {
        return static::connection()->zScore($this->getSortedSetKey(), $member);
    }

    /**
     * 计算在有序集合中指定区间分数的成员数
     * @param string $start
     * @param string $end
     * @return false|int
     */
    public function zCount(string $start, string $end)
    {
        return static::connection()->zCount($this->getSortedSetKey(), $start, $end);
    }

    /**
     * 通过索引区间返回有序集合指定区间内的成员
     * @param string $start
     * @param string $stop
     * @param string $by
     * @param string $rev
     * @param array $options
     * @return false|array
     * @link https://redis.io/commands/zrange/
     */
    public function zRange(string $start, string $stop, string $by = 'BYSCORE', string $rev = 'REV', array $options = ['LIMIT', 0, 128])
    {
        return static::connection()->zRange($this->getSortedSetKey(), ... func_get_args());
    }

    /**
     * 返回有序集中指定分数区间内的成员
     * - 有序集成员按分数值递减(从大到小)的次序排列
     * @param string $start
     * @param string $end
     * @param array $options
     * @return false|array
     */
    public function zRevRangeByScore(string $start, string $end = '-inf', array $options = ['LIMIT', 0, 128])
    {
        return static::connection()->zRevRangeByScore($this->getSortedSetKey(), $start, $end, $options);
    }

    /**
     * 通过分数返回有序集合指定区间内的成员
     * - 有序集成员按分数值递增(从小到大)次序排列
     * @param string $min
     * @param string $max
     * @param array $options
     * @return false|array
     */
    public function zRangeByScore(string $min = '-inf', string $max = '+inf', array $options = ['LIMIT', 0, 128])
    {
        return static::connection()->zRangeByScore($this->getSortedSetKey(), $min, $max, $options);
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
