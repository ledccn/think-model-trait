# 说明


## 安装

`composer install ledc/think-model-trait`

## 使用说明

例如：数据库配置文件`config/database.php`内，生成 数据库连接`mysql`内的`user`表注释，命令如下

```shell
php think make:trait mysql user
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