<?php

namespace Ledc\ThinkModelTrait\Contracts;

use BadMethodCallException;
use InvalidArgumentException;
use think\App;
use think\Container;
use think\helper\Str;

/**
 * 驱动管理器
 */
abstract class Manager
{
    /**
     * 驱动
     * @var array
     */
    protected array $drivers = [];
    /**
     * 驱动的命名空间
     * @var string|null
     */
    protected ?string $namespace = null;
    /**
     * 使用容器创建对象时，始终创建新的驱动对象实例
     * @var bool
     */
    protected bool $alwaysNewInstance = false;

    /**
     * 默认驱动
     * @return string
     */
    abstract public function getDefaultDriver(): string;

    /**
     * 获取驱动实例
     * @param null|string $name
     * @return mixed
     */
    protected function driver(?string $name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        if (is_null($name)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL driver for [%s].',
                static::class
            ));
        }

        return $this->drivers[$name] = $this->getDriver($name);
    }

    /**
     * 获取驱动实例
     * @param string $name
     * @return mixed
     */
    final protected function getDriver(string $name)
    {
        return $this->drivers[$name] ?? $this->createDriver($name);
    }

    /**
     * 获取驱动类型
     * @param string $name
     * @return string
     */
    protected function resolveType(string $name): string
    {
        return $name;
    }

    /**
     * 获取驱动配置
     * @param string $name
     * @return mixed
     */
    protected function resolveConfig(string $name)
    {
        return $name;
    }

    /**
     * 获取驱动参数
     * @param string $name
     * @return array
     */
    protected function resolveParams(string $name): array
    {
        $config = $this->resolveConfig($name);
        return [$config];
    }

    /**
     * 获取驱动类
     * @param string $type
     * @return string
     */
    final protected function resolveClass(string $type): string
    {
        if ($this->namespace || false !== strpos($type, '\\')) {
            $class = false !== strpos($type, '\\') ? $type : $this->namespace . Str::studly($type);

            if (class_exists($class)) {
                return $class;
            }
        }

        throw new InvalidArgumentException("Driver [$type] not supported.");
    }

    /**
     * 创建驱动
     * @param string $name
     * @return mixed
     *
     */
    final protected function createDriver(string $name)
    {
        $type = $this->resolveType($name);
        $params = $this->resolveParams($name);

        // 从方法创建
        $method = 'create' . Str::studly($type) . 'Driver';
        if (method_exists($this, $method)) {
            return $this->$method(...$params);
        }

        // 从容器创建
        if (app()->bound($name)) {
            $newInstance = $this->alwaysNewInstance;
            return static::app()->make($name, $params, $newInstance);
        }

        // 从命名空间创建
        $class = $this->resolveClass($type);
        return static::app()->invokeClass($class, $params);
    }

    /**
     * 移除一个驱动实例
     * @param array|string|null $name
     * @return static
     */
    final public function forgetDriver($name = null): Manager
    {
        $name = $name ?? $this->getDefaultDriver();

        foreach ((array)$name as $cacheName) {
            if (isset($this->drivers[$cacheName])) {
                unset($this->drivers[$cacheName]);
            }
        }

        return $this;
    }

    /**
     * 清理所有驱动实例
     * @return void
     */
    final public function clearDriver(): void
    {
        $keys = array_keys($this->drivers);
        foreach ($keys as $key) {
            unset($this->drivers[$key]);
        }
    }

    /**
     * 快速获取容器中的实例 支持依赖注入
     * @template T
     * @param string|class-string<T> $name 类名或标识 默认获取当前应用实例
     * @param array $args 参数
     * @param bool $newInstance 是否每次创建新的实例
     * @return T|object|App
     */
    public static function app(string $name = '', array $args = [], bool $newInstance = false)
    {
        return Container::getInstance()->make($name ?: App::class, $args, $newInstance);
    }

    /**
     * 获取容器中的对象实例 不存在则创建（单例模式）
     * @return static
     */
    public static function getInstance(): Manager
    {
        return Container::pull(static::class);
    }

    /**
     * 动态调用
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }

    /**
     * 在静态上下文中调用一个不可访问方法时，__callStatic() 会被调用
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $driver = static::getInstance()->driver();
        if (method_exists($driver, $name) && is_callable([$driver, $name])) {
            return $driver->{$name}(... $arguments);
        }
        throw new BadMethodCallException('未定义的方法：' . $name);
    }
}
