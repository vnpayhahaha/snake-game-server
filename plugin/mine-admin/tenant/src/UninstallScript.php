<?php

namespace Plugin\MineAdmin\Tenant;

use App\Model\Permission\Menu;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;
use Nette\Utils\FileSystem;
use Hyperf\Command\Concerns\InteractsWithIO;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ApplicationInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class UninstallScript
{
    use InteractsWithIO;

    public function __construct()
    {
        global $argv;
        $this->input = new ArrayInput($argv);
        $this->output = new SymfonyStyle($this->input,new ConsoleOutput());
    }

    public function __invoke()
    {
        $app = ApplicationContext::getContainer()->get(ApplicationInterface::class);
        $app->setAutoExit(false);

        $isUninstall = $this->output->ask('确定卸载多租户插件吗?','no');

        if (in_array($isUninstall, ['n', 'N', 'NO', 'no'])) {

            $this->info('为保证数据安全，将不会删除 [多租户数据表] 跟自带表加入的 tenant_id 字段');
            $this->alert('将开始卸载多租户插件，但后端 [plugin/mine-admin/tenant] 将保留，请手动删除');

            $webRoot = BASE_PATH . '/web/src';
            if (file_exists($webRoot . '/modules/base/views/login/components/login-form.vue.back')) {
                // 还原原登录表单页面
                Filesystem::rename(
                    $webRoot . '/modules/base/views/login/components/login-form.vue.back',
                    $webRoot . '/modules/base/views/login/components/login-form.vue'
                );
            }
            if (is_dir($webRoot . '/plugins/mine-admin/tenant')) {
                // 删除前端插件
                Filesystem::delete($webRoot . '/plugins/mine-admin/tenant');
            }
            if (file_exists(BASE_PATH . '/plugin/mine-admin/tenant/install.lock')) {
                // 删除插件 install.lock 文件
                Filesystem::delete(BASE_PATH . '/plugin/mine-admin/tenant/install.lock');
            }

            // 删除菜单
            Menu::query()->where('name', 'like', 'plugin:mine-admin:tenant%')->delete();
        }
    }
}