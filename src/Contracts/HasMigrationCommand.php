<?php

namespace Ledc\ThinkModelTrait\Contracts;

use InvalidArgumentException;
use Phinx\Util\Util;
use RuntimeException;
use think\console\Input;
use think\console\Output;

/**
 * 【特性】创建数据库迁移文件
 */
trait HasMigrationCommand
{
    /**
     * 迁移文件映射
     * - 键为迁移文件类名，值为迁移文件模板文件路径
     * @var array
     */
    private array $fileMaps = [];

    /**
     * 获取迁移文件映射
     * @return array 键为迁移文件类名，值为迁移文件模板文件路径
     */
    public function getFileMaps(): array
    {
        return $this->fileMaps;
    }

    /**
     * 设置迁移文件映射
     * @param array $fileMaps 键为迁移文件类名，值为迁移文件模板文件路径
     */
    public function setFileMaps(array $fileMaps): void
    {
        $this->fileMaps = $fileMaps;
    }

    /**
     * 批量创建迁移文件
     * @param Input $input
     * @param Output $output
     * @return void
     */
    public function eachFileMaps(Input $input, Output $output): void
    {
        foreach ($this->getFileMaps() as $className => $templateFilepath) {
            $path = $this->migrationCreate($className, $templateFilepath);
            $output->writeln('<info>created</info> .' . str_replace(getcwd(), '', realpath($path)));
            sleep(2);
        }
    }

    /**
     * 创建迁移文件
     * @param string $className
     * @param string $templateFilepath
     * @return string
     */
    protected function migrationCreate(string $className, string $templateFilepath): string
    {
        $path = $this->ensureDirectory();

        if (!Util::isValidPhinxClassName($className)) {
            throw new InvalidArgumentException(sprintf('The migration class name "%s" is invalid. Please use CamelCase format.', $className));
        }

        if (!Util::isUniqueMigrationClassName($className, $path)) {
            throw new InvalidArgumentException(sprintf('The migration class name "%s" already exists', $className));
        }

        // Compute the file path
        $fileName = Util::mapClassNameToFileName($className);
        $filePath = $path . DIRECTORY_SEPARATOR . $fileName;

        if (is_file($filePath)) {
            throw new InvalidArgumentException(sprintf('The file "%s" already exists', $filePath));
        }

        if (false === file_put_contents($filePath, file_get_contents($templateFilepath))) {
            throw new RuntimeException(sprintf('The file "%s" could not be written to', $path));
        }

        return $filePath;
    }

    /**
     * 确保目录存在
     * @return string
     */
    protected function ensureDirectory(): string
    {
        $path = app()->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'migrations';

        if (!is_dir($path) && !mkdir($path, 0755, true)) {
            throw new InvalidArgumentException(sprintf('directory "%s" does not exist', $path));
        }

        if (!is_writable($path)) {
            throw new InvalidArgumentException(sprintf('directory "%s" is not writable', $path));
        }

        return $path;
    }
}