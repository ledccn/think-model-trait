<?php

namespace Ledc\ThinkModelTrait\Contracts;

use InvalidArgumentException;
use JsonSerializable;

/**
 * 参数抽象类
 */
abstract class Parameters implements JsonSerializable
{
    /**
     * 构造函数
     * @param array $properties
     * @return void
     */
    public function __construct(array $properties = [])
    {
        foreach ($properties as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * 转数组
     * @return array
     */
    public function jsonSerialize(): array
    {
        $items = [];
        $excludesKeys = $this->getExcludesKeys();
        $properties = $this->filterEmptyValues(get_object_vars($this));
        foreach ($properties as $key => $value) {
            // 排除的keys
            if (in_array($key, $excludesKeys, true)) {
                continue;
            }

            if ($value instanceof JsonSerializable) {
                $items[$key] = $value->jsonSerialize();
            } else {
                $items[$key] = $value;
            }
        }

        return $this->checkMissingKeys($items);
    }

    /**
     * 转数组
     * @return array
     */
    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    /**
     * 转字符串
     * @param int $options
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        $json = json_encode($this->jsonSerialize(), $options);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException('json_encode error: ' . json_last_error_msg());
        }

        return $json;
    }

    /**
     * 过滤空值
     * @param array $properties
     * @return array
     */
    protected function filterEmptyValues(array $properties): array
    {
        return array_filter($properties, fn($value) => !is_null($value) && '' !== $value && [] !== $value);
    }

    /**
     * 验证必填参数
     * @param array $properties
     * @return array
     */
    private function checkMissingKeys(array $properties): array
    {
        $requiredKeys = $this->getRequiredKeys();
        if (!empty($requiredKeys)) {
            $missingKeys = [];
            foreach ($requiredKeys as $key) {
                if (!isset($properties[$key])) {
                    $missingKeys[] = $key;
                }
            }

            if (!empty($missingKeys)) {
                throw new InvalidArgumentException("缺少必填参数：" . implode(',', $missingKeys));
            }
        }

        return $properties;
    }

    /**
     * 获取必填的key
     * @return array
     */
    abstract protected function getRequiredKeys(): array;

    /**
     * 获取排除的key
     * @return array
     */
    protected function getExcludesKeys(): array
    {
        return [];
    }
}
