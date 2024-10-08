# 批量生成thinkPHP项目的数据表注释

## 安装

`composer require ledc/think-model-trait`

## 使用说明

### 生成数据表字段注释

例如：数据库配置文件`config/database.php`，生成 数据库连接`mysql`、数据表`user`的表注释，命令如下

```shell
php think make:trait mysql user
```

### 清空数据表

```shell
php think clear:table mysql YmdHi --file=exclude --force
```

## 帮助指令
```shell
php think make:trait -h
Usage:
  make:trait [options] [--] <connection> <table>

Arguments:
  connection            数据库连接名称
  table                 完整的数据表名称

Options:
      --m               同时生成模型
```

## 捐赠

![reward](reward.png)