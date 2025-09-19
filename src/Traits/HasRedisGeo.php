<?php

namespace Ledc\ThinkModelTrait\Traits;

use BadMethodCallException;
use Ledc\ThinkModelTrait\RedisUtils;
use Redis;
use RedisException;

/**
 * Redis GEO地理位置
 * @method array geoRadiusByMember($member, $radius, $units, $options = []) 根据储存在位置集合里面的某个地点获取指定范围内的地理位置集合
 */
trait HasRedisGeo
{
    /**
     * GEO地理位置key
     * @var string
     */
    protected string $geoKey;

    /**
     * @param string $geoKey
     * @return HasRedisGeo
     */
    protected function setGeoKey(string $geoKey): self
    {
        $this->geoKey = $geoKey;
        return $this;
    }

    /**
     * 获取
     * @return string
     */
    public function getGeoKey(): string
    {
        return $this->geoKey;
    }

    /**
     * 移除有序集合中指定的成员。
     * @param string $member
     * @return int|false
     */
    public function zRem(string $member)
    {
        return static::connection()->zRem($this->getGeoKey(), $member);
    }

    /**
     * 添加地理位置的坐标
     * @param string $longitude 经度（东西位置）
     * @param string $latitude 纬度（南北位置）
     * @param string $member 成员名
     * @return int|false
     */
    public function geoAdd(string $longitude, string $latitude, string $member)
    {
        return static::connection()->geoAdd($this->getGeoKey(), $longitude, $latitude, $member);
    }

    /**
     * 返回一个或多个位置对象的 geoHash 值
     * @param string|array $members
     * @return array|false|string[] 返回geoHash 值的一维数组，格式为["w7w8884z990", "w7w8884z990", false]，其中false表示失败
     */
    public function geoHash($members)
    {
        if (is_string($members)) {
            return static::connection()->geoHash($this->getGeoKey(), $members);
        } else {
            return static::connection()->geoHash($this->getGeoKey(), ...$members);
        }
    }

    /**
     * 获取地理位置的坐标
     * @param string|array $members
     * @return array|false 经纬度数组，二维数组：[[$longitude, $latitude], [$longitude, $latitude], []]，其中空数组表示失败
     */
    public function geoPos($members)
    {
        if (is_string($members)) {
            return static::connection()->geoPos($this->getGeoKey(), $members);
        } else {
            return static::connection()->geoPos($this->getGeoKey(), ...$members);
        }
    }

    /**
     * 返回地理空间集合中两个成员之间的距离
     * @param string $member1 成员1
     * @param string $member2 成员2
     * @param string $unit 距离单位，默认：m米（m:米，km:千米，mi:英里，ft:英尺）
     * @return float|false 返回布尔值false表示失败，返回字符串表示距离
     * @throws RedisException
     */
    public function geoDist(string $member1, string $member2, string $unit = Constants::UNIT_KM)
    {
        return static::connection()->geodist($this->getGeoKey(), $member1, $member2, $unit);
    }

    /**
     * 查询地理位置范围内符合条件的成员
     * - 该方法用于检索某个地理位置坐标范围内指定半径内的所有成员，并支持多种选项来控制返回结果的格式和内容。
     * @param float $lng 查询中心点的经度
     * @param float $lat 查询中心点的纬度
     * @param float $radius 搜索半径
     * @param string $unit 距离单位（如 'm'、'km'、'mi'、'ft'）
     * @param array $options 可选参数数组，控制返回结果格式
     * @return mixed
     */
    public function geoRadius(float $lng, float $lat, float $radius, string $unit = Constants::UNIT_M, array $options = [])
    {
        return static::connection()->georadius($this->getGeoKey(), $lng, $lat, $radius, $unit, $options);
    }

    /**
     * 以各种方式搜索地理空间集合中的成员
     * @param array|string $position 一个包含经纬度的数组，或一个集合成员的字符串【示例 [$longitude, $latitude] 或 $member】
     * @param array|int|float $shape 一个数字表示搜索的圆的半径，或者一个双元素数组表示要搜索的框的宽度和高度
     * @param string $unit 距离单位
     * @param array $options 其他选项
     * @return array
     */
    public function geoSearch($position, $shape, string $unit = Constants::UNIT_M, array $options = []): array
    {
        return static::connection()->geosearch($this->getGeoKey(), $position, $shape, $unit, $options);
    }

    /**
     * 在给定区域或范围内搜索地理空间排序集的成员，并将结果存储到新集合中
     * @param string $dst
     * @param string $src
     * @param array|string $position
     * @param array|int|float $shape
     * @param string $unit 距离单位
     * @param array $options 其他选项
     * @return array|int|false
     */
    public function geoSearchStore(string $dst, string $src, $position, $shape, string $unit = Constants::UNIT_M, array $options = [])
    {
        return static::connection()->geosearchstore($dst, $src, $position, $shape, $unit, $options);
    }


    /**
     * 动态调用
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (0 !== strpos(strtolower($name), 'geo')) {
            throw new BadMethodCallException('Call to undefined method ' . self::class . '::' . $name . '()');
        }
        return $this->connection()->{$name}($this->getGeoKey(), ...$arguments);
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
