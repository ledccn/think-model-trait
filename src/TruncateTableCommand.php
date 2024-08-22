<?php
declare (strict_types=1);

namespace Ledc\ThinkModelTrait;

use InvalidArgumentException;
use RuntimeException;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Throwable;

/**
 * 清空数据表
 */
class TruncateTableCommand extends Command
{
    /**
     * 清空数据表时，排除的数据表
     * @description CRMEB单商户
     */
    protected array $exclude = [
        'eb_agent_level',
        'eb_agreement',
        'eb_category',
        'eb_diy',
        'eb_express',
        'eb_lang_code',
        'eb_lang_country',
        'eb_lang_type',
        'eb_member_right',
        'eb_member_ship',
        'eb_message_system',
        'eb_out_interface',
        'eb_page_categroy',
        'eb_page_link',
        'eb_shipping_templates',
        'eb_shipping_templates_region',
        'eb_store_service_speechcraft',
        'eb_system_admin',
        'eb_system_attachment',
        'eb_system_attachment_category',
        'eb_system_city',
        'eb_system_config',
        'eb_system_config_tab',
        'eb_system_event_data',
        'eb_system_group',
        'eb_system_group_data',
        'eb_system_menus',
        'eb_system_notification',
        'eb_system_route',
        'eb_system_route_cate',
        'eb_system_timer',
        'eb_system_user_level',
        'eb_user_group',
        'eb_user_label',
        'eb_wechat_qrcode_cate',
    ];

    /**
     * @return void
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('clear:table')
            ->addArgument('connection', Argument::REQUIRED, "数据库连接名称")
            ->addArgument('password', Argument::REQUIRED, "管理密码")
            ->addOption('file', null, Option::VALUE_OPTIONAL, '清空数据表时，排除的数据表')
            ->addOption('force', null, Option::VALUE_NONE, '强制清理')
            ->setDescription('清空数据表');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        try {
            $connection = $input->getArgument('connection');
            $password = $input->getArgument('password');
            $file = $input->getOption('file');
            $force = $input->hasOption('force');

            // 验证密码
            $date = date('YmdHi');
            if ($password !== $date) {
                throw new InvalidArgumentException('密码错误：' . $date);
            }

            // 清空数据表时，排除的数据表
            $exclude = $this->exclude;
            if (!empty($file)) {
                $filename = runtime_path() . $file . '.php';
                if (!is_file($filename)) {
                    throw new RuntimeException('文件不存在：' . $filename);
                }

                $_exclude = include $filename;
                if (empty($_exclude) || !is_array($_exclude)) {
                    throw new RuntimeException('排除不清理的数据表为空或非数组');
                }
                $exclude = $_exclude;
            }

            // 循环清空表
            $connect = Util::getConnect($connection);
            $tables = Util::getTables($connection);
            if (empty($tables)) {
                $output->writeln("当前数据库连接{$connection}，数据表为空，不需要清理");
            } else {
                if (empty(array_intersect($tables, $exclude))) {
                    throw new RuntimeException('当前数据库连接现有数据表与排除表的交集为空');
                }

                $output->writeln('执行中...');
                $connect->query("SET FOREIGN_KEY_CHECKS = 0");
                foreach ($tables as $table) {
                    if (!in_array($table, $exclude)) {
                        $sql = "TRUNCATE `$table`";
                        if ($force) {
                            $connect->query($sql);
                        } else {
                            $output->writeln('待清理数据表：' . $table);
                        }
                    }
                }
                $connect->query("SET FOREIGN_KEY_CHECKS = 1");
            }

            // 指令输出
            $output->writeln($force ? '已清空数据表' : '携带参数：--force 强制清空数据表');
        } catch (Throwable $e) {
            // 指令输出
            $output->writeln($e->getMessage());
        }
    }
}
