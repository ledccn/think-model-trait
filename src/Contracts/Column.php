<?php

namespace Ledc\ThinkModelTrait\Contracts;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * 数据库迁移，列定义
 */
class Column extends \Phinx\Db\Table\Column
{
    /**
     * @var bool
     */
    protected bool $unique = false;

    /**
     * 设置可空
     * @return self
     */
    public function setNullable(): self
    {
        return $this->setNull(true);
    }

    /**
     * 设置无符号
     * @return self
     */
    public function setUnsigned(): self
    {
        return $this->setSigned(false);
    }

    /**
     * 设置唯一索引
     * @return self
     */
    public function setUnique(): self
    {
        $this->unique = true;
        return $this;
    }

    /**
     * 是否唯一索引
     * @return bool
     */
    public function getUnique(): bool
    {
        return $this->unique;
    }

    /**
     * 是否唯一索引
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->getUnique();
    }

    /**
     * 创建列
     * @param string $name 列名
     * @param string $type 列类型
     * @param array $options 列属性
     * @return self
     */
    public static function make(string $name, string $type, array $options = []): self
    {
        $column = new static();
        $column->setName($name);
        $column->setType($type);
        $column->setOptions($options);
        return $column;
    }

    /**
     * 创建 BIGINT 列
     * @param string $name 列名
     * @return self
     */
    public static function bigInteger(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_BIG_INTEGER);
    }

    /**
     * 创建 INT 列
     * @param string $name 列名
     * @return self
     */
    public static function integer(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_INTEGER);
    }

    /**
     * 创建 MEDIUMINT 列
     * @param string $name
     * @return self
     */
    public static function mediumInteger(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_INTEGER, ['length' => MysqlAdapter::INT_MEDIUM]);
    }

    /**
     * 创建 SMALLINT 列
     * @param string $name
     * @return self
     */
    public static function smallInteger(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_INTEGER, ['length' => MysqlAdapter::INT_SMALL]);
    }

    /**
     * 创建 TINYINT 列
     * @param string $name
     * @return self
     */
    public static function tinyInteger(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_INTEGER, ['length' => MysqlAdapter::INT_TINY]);
    }

    /**
     * 创建 UNSIGNED INT 列
     * @param string $name 列名
     * @param bool $nullable 是否可空
     * @return self
     */
    public static function unsignedInteger(string $name, bool $nullable = false): self
    {
        return self::integer($name)->setUnSigned()->setNull($nullable);
    }

    /**
     * 创建 UNSIGNED MEDIUMINT 列
     * @param string $name 列名
     * @param bool $nullable 是否可空
     * @return self
     */
    public static function unsignedMediumInteger(string $name, bool $nullable = false): self
    {
        return self::integer($name)->setUnSigned()->setLimit(MysqlAdapter::INT_MEDIUM)->setNull($nullable);
    }

    /**
     * 创建 UNSIGNED SMALLINT 列
     * @param string $name 列名
     * @param bool $nullable 是否可空
     * @return self
     */
    public static function unsignedSmallInteger(string $name, bool $nullable = false): self
    {
        return self::integer($name)->setUnSigned()->setLimit(MysqlAdapter::INT_SMALL)->setNull($nullable);
    }

    /**
     * 创建 UNSIGNED TINYINT 列
     * @param string $name 列名
     * @param bool $nullable 是否可空
     * @return self
     */
    public static function unsignedTinyInteger(string $name, bool $nullable = false): self
    {
        return self::tinyInteger($name)->setUnSigned()->setNull($nullable);
    }

    /**
     * 创建二进制列
     * @param string $name 列名
     * @return self
     */
    public static function binary(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_BLOB);
    }

    /**
     * 创建布尔列
     * @param string $name 列名
     * @return self
     */
    public static function boolean(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_BOOLEAN)->setNull(false);
    }

    /**
     * 创建 CHAR 列
     * @param string $name 列名
     * @param int $length 字符长度
     * @return self
     */
    public static function char(string $name, int $length = 255): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_CHAR, compact('length'));
    }

    /**
     * 创建 DATE 列
     * @param string $name 列名
     * @return self
     */
    public static function date(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_DATE);
    }

    /**
     * 创建 DATETIME 列
     * @param string $name 列名
     * @return self
     */
    public static function dateTime(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_DATETIME);
    }

    /**
     * 创建 DECIMAL 列
     * @param string $name 列名
     * @param int $precision 精度
     * @param int $scale 小数位数
     * @param bool $signed
     * @param bool $nullable
     * @return self
     */
    public static function decimal(string $name, int $precision = 8, int $scale = 2, bool $signed = false, bool $nullable = false): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_DECIMAL, compact('precision', 'scale'))
            ->setSigned($signed)
            ->setNull($nullable);
    }

    /**
     * 创建 ENUM 列
     * @param string $name 列名
     * @param array $values 枚举值
     * @return self
     */
    public static function enum(string $name, array $values): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_ENUM, compact('values'));
    }

    /**
     * 创建 FLOAT 列
     * @param string $name 列名
     * @return self
     */
    public static function float(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_FLOAT);
    }

    /**
     * 创建 JSON 列
     * @param string $name
     * @return self
     */
    public static function json(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_JSON);
    }

    /**
     * 创建 JSONB 列
     * @param string $name
     * @return self
     */
    public static function jsonb(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_JSONB);
    }

    /**
     * 创建 LONGTEXT 列
     * @param string $name
     * @return self
     */
    public static function longText(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_TEXT, ['length' => MysqlAdapter::TEXT_LONG]);
    }

    /**
     * 创建 MEDIUMTEXT 列
     * @param string $name
     * @return self
     */
    public static function mediumText(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_TEXT, ['length' => MysqlAdapter::TEXT_MEDIUM]);
    }

    /**
     * 创建 STRING 列
     * @param string $name
     * @param int $length
     * @return self
     */
    public static function string(string $name, int $length = 255): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_STRING, compact('length'));
    }

    /**
     * 创建 TEXT 列
     * @param string $name
     * @return self
     */
    public static function text(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_TEXT);
    }

    /**
     * 创建 TIME 列
     * @param string $name
     * @return self
     */
    public static function time(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_TIME);
    }

    /**
     * 创建 TIMESTAMP 列
     * @param string $name
     * @return self
     */
    public static function timestamp(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_TIMESTAMP);
    }

    /**
     * 创建 UUID 列
     * @param string $name
     * @return self
     */
    public static function uuid(string $name): self
    {
        return self::make($name, AdapterInterface::PHINX_TYPE_UUID);
    }

    /**
     * 构造创建时间列
     * @return self
     */
    public static function makeCreateTime(): self
    {
        return self::make('create_time', AdapterInterface::PHINX_TYPE_DATETIME, ['comment' => '创建时间', 'null' => false, 'default' => 'CURRENT_TIMESTAMP']);
    }

    /**
     * 构造更新时间列
     * @return self
     */
    public static function makeUpdateTime(): self
    {
        return self::make('update_time', AdapterInterface::PHINX_TYPE_DATETIME, ['comment' => '更新时间', 'null' => true, 'default' => 'CURRENT_TIMESTAMP']);
    }

    /**
     * 构造删除时间列
     * @return self
     */
    public static function makeDeleteTime(): self
    {
        return self::make('delete_time', AdapterInterface::PHINX_TYPE_DATETIME, ['comment' => '删除时间', 'null' => true, 'default' => null]);
    }

    /**
     * 构造是否启用列
     * @return self
     */
    public static function makeEnabled(): self
    {
        return self::make('enabled', AdapterInterface::PHINX_TYPE_INTEGER, ['comment' => '是否启用', 'null' => false, 'default' => 1, 'limit' => MysqlAdapter::INT_TINY, 'signed' => false]);
    }

    /**
     * 构造状态列
     * @return self
     */
    public static function makeStatus(): self
    {
        return self::make('status', AdapterInterface::PHINX_TYPE_INTEGER, ['comment' => '状态', 'null' => false, 'default' => 0, 'limit' => MysqlAdapter::INT_TINY]);
    }

    /**
     * 构造排序列
     * @return self
     */
    public static function makeSort(): self
    {
        return self::make('sort', AdapterInterface::PHINX_TYPE_INTEGER, ['comment' => '排序', 'null' => false, 'default' => 100, 'limit' => MysqlAdapter::INT_SMALL, 'signed' => false]);
    }

    /**
     * 构造 UNSIGNED INT 列
     * @param string $name 列名
     * @param string $comment 列注释
     * @param int $default 默认值
     * @param bool $nullable 是否可空
     * @return self
     */
    public static function makeUnsignedInteger(string $name, string $comment, int $default = 0, bool $nullable = false): self
    {
        return self::integer($name)->setUnSigned()->setComment($comment)->setDefault($default)->setNull($nullable);
    }

    /**
     * 构造 UNSIGNED TINYINT 列
     * @param string $name 列名
     * @param string $comment 列注释
     * @param int $default 默认值
     * @param bool $nullable 是否可空
     * @return self
     */
    public static function makeUnsignedTinyInteger(string $name, string $comment, int $default = 0, bool $nullable = false): self
    {
        return self::tinyInteger($name)->setUnSigned()->setComment($comment)->setDefault($default)->setNull($nullable);
    }

    /**
     * 构造 VARCHAR 列
     * @param string $name 列名
     * @param string $comment 列注释
     * @param int $length 列长度
     * @param string $default 默认值
     * @param bool $nullable 是否可空
     * @return self
     */
    public static function makeString(string $name, string $comment, int $length = 255, string $default = '', bool $nullable = false): self
    {
        return self::string($name, $length)->setComment($comment)->setDefault($default)->setNull($nullable);
    }
}
