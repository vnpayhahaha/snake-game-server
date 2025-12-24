<?php

namespace Plugin\MineAdmin\Tenant;

use App\Model\Permission\Menu;
use App\Model\Permission\Meta;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;
use Nette\Utils\FileSystem;
use Hyperf\Command\Concerns\InteractsWithIO;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ApplicationInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallScript
{
    use InteractsWithIO;

    protected const BASE_MENU_DATA = [
        'name' => '',
        'path' => '',
        'component' => '',
        'redirect' => '',
        'created_by' => 0,
        'updated_by' => 0,
        'remark' => '',
    ];

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

        $this->info('即将安装 [mine-admin/tenant] 插件、将会进行以下操作，请确认无误后继续：');
        $this->table(['说明', '操作'], [
            ['替换前端登录页面，原文件将添加后缀：.back', '替换'],
        ]);

        $isDepartment = $this->output->ask('使用的分支是否为部门分支 [master-department] 代码','no');

        $tables = $this->getTableList($isDepartment);

        $this->info('以下表将会在安装时加入 tenant_id 字段：');
        $this->table(['表名称'], $tables);

        $this->addColumn($tables);

        $webRoot = BASE_PATH . '/web/src';
        $component = $webRoot . '/modules/base/views/login/components';
        $temp = BASE_PATH . '/plugin/mine-admin/tenant/src/Temp';
        if (file_exists("{$component}/login-form.vue")) {
            // 重命名原登录表单页面
            Filesystem::rename(
                "{$component}/login-form.vue",
                "{$component}/login-form.vue.back"
            );
        }

        if (file_exists("{$temp}/login-form.vue")) {

            // 复制登录表单页面
            FileSystem::copy(
                "{$temp}/login-form.vue",
                "{$component}/login-form.vue"
            );
        }

        // 插入菜单
        $this->insertMenu();
    }

    protected function getTableList(string $isDepartment): array
    {
        $default = [ ['user'], ['role'], ['user_operation_log'], ['user_login_log'], ['attachment'] ];
        if (in_array($isDepartment, ['yes', 'y', 'YES', 'Y'])) {
            foreach ([['department'], ['position']] as $table) {
               $default[] = $table;
            }
            return $default;
        }
        return $default;
    }

    protected function addColumn(array $tables): void
    {
        foreach ($tables as $tbName) {
            if (Schema::hasTable($tbName[0]) && !Schema::hasColumn($tbName[0], 'tenant_id')) {
                Schema::table($tbName[0], function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0);
                    $table->index('tenant_id');
                });
            }
        }
    }

    protected function insertMenu(): void
    {
        // 菜单
        $menu = [
            [
                'name' => 'plugin:mine-admin:tenant-manage',
                'parent_id' => 1,
                'path' => '/tenant',
                'meta' => [
                    'title' => '多租户',
                    'i18n' => 'mineTenant.menu.tenantManage',
                    'icon' => 'material-symbols-light:tenancy-outline',
                    'type' => 'M',
                    'hidden' => false,
                    'breadcrumbEnable' => 1,
                    'copyright' => 1,
                    'cache' => 1,
                    'affix' => 0,
                ],
                'children' => [
                    [
                        'name' => 'plugin:mine-admin:tenant-package',
                        'path' => '/tenant/package',
                        'component' => 'mine-admin/tenant/views/tenant-package/index',
                        'meta' => [
                            'title' => '套餐管理',
                            'i18n' => 'mineTenant.menu.packageManage',
                            'icon' => 'mage:package-box',
                            'type' => 'M',
                            'hidden' => false,
                            'breadcrumbEnable' => 1,
                            'copyright' => 1,
                            'cache' => 1,
                            'componentPath' => 'plugins/',
                            'componentSuffix' => '.vue',
                            'affix' => 0,
                        ],
                        'children' => [
                            [
                                'name' => 'plugin:mine-admin:tenant-package:list',
                                'meta' => new Meta([
                                    'title' => '套餐列表',
                                    'type' => 'B',
                                    'i18n' => 'mineTenant.menu.packageList',
                                ]),
                            ],
                            [
                                'name' => 'plugin:mine-admin:tenant-package:create',
                                'meta' => new Meta([
                                    'title' => '新增套餐',
                                    'type' => 'B',
                                    'i18n' => 'mineTenant.menu.packageSave',
                                ]),
                            ],
                            [
                                'name' => 'plugin:mine-admin:tenant-package:save',
                                'meta' => new Meta([
                                    'title' => '更新套餐',
                                    'type' => 'B',
                                    'i18n' => 'mineTenant.menu.packageUpdate',
                                ]),
                            ],
                            [
                                'name' => 'plugin:mine-admin:tenant-package:delete',
                                'meta' => new Meta([
                                    'title' => '删除套餐',
                                    'type' => 'B',
                                    'i18n' => 'mineTenant.menu.packageDelete',
                                ]),
                            ],
                        ],

                    ],
                    [
                        'name' => 'plugin:mine-admin:tenant',
                        'path' => '/tenant/manage',
                        'component' => 'mine-admin/tenant/views/tenant/index',
                        'meta' => [
                            'title' => '租户管理',
                            'i18n' => 'mineTenant.menu.tenant',
                            'icon' => 'material-symbols:person-play-rounded',
                            'type' => 'M',
                            'hidden' => false,
                            'breadcrumbEnable' => 1,
                            'copyright' => 1,
                            'cache' => 1,
                            'componentPath' => 'plugins/',
                            'componentSuffix' => '.vue',
                            'affix' => 0,
                        ],
                        'children' => [
                            [
                                'name' => 'plugin:mine-admin:tenant:list',
                                'meta' => new Meta([
                                    'title' => '租户列表',
                                    'type' => 'B',
                                    'i18n' => 'mineTenant.menu.tenantIndex',
                                ]),
                            ],
                            [
                                'name' => 'plugin:mine-admin:tenant:create',
                                'meta' => new Meta([
                                    'title' => '新增租户',
                                    'type' => 'B',
                                    'i18n' => 'mineTenant.menu.tenantSave',
                                ]),
                            ],
                            [
                                'name' => 'plugin:mine-admin:tenant:save',
                                'meta' => new Meta([
                                    'title' => '更新租户',
                                    'type' => 'B',
                                    'i18n' => 'mineTenant.menu.tenantUpdate',
                                ]),
                            ],
                            [
                                'name' => 'plugin:mine-admin:tenant:delete',
                                'meta' => new Meta([
                                    'title' => '删除租户',
                                    'type' => 'B',
                                    'i18n' => 'mineTenant.menu.tenantDelete',
                                ]),
                            ],
                        ],

                    ],
                ]
            ],
        ];

        $this->executeMenuQuery($menu);
    }

    private function executeMenuQuery(array $data, int $parent_id = 0): void
    {
        foreach ($data as $v) {
            $_v = $v;
            if (isset($v['children'])) {
                unset($_v['children']);
            }
            if (empty($_v['parent_id'])) {
                $_v['parent_id'] = $parent_id;
            }
            $menu = Menu::create(array_merge(self::BASE_MENU_DATA, $_v));
            if (isset($v['children']) && count($v['children'])) {
                $this->executeMenuQuery($v['children'], $menu->id);
            }
        }
    }
}