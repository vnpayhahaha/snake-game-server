<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Plugin\MineAdmin\Crontab;

use App\Model\Permission\Menu;
use App\Model\Permission\Meta;
use Composer\InstalledVersions;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ApplicationInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class InstallScript
{
    public const BASE_MENU_DATA = [
        'name' => '',
        'path' => '',
        'component' => '',
        'redirect' => '',
        'created_by' => 0,
        'updated_by' => 0,
        'remark' => '',
    ];

    public function __invoke()
    {
        if (! InstalledVersions::isInstalled('mineadmin/crontab')) {
            throw new \RuntimeException('mineadmin/crontab 未安装,请执行 composer require mineadmin/crontab');
        }
        $app = ApplicationContext::getContainer()->get(ApplicationInterface::class);
        $app->setAutoExit(false);

        $app->run(new ArrayInput(['crontab:migrate']), new NullOutput());

        // 菜单
        $menu = [
            [
                'name' => 'plugin:mine-admin:crontab',
                'path' => '/mine-crontab',
                'component' => 'mine-admin/crontab/views/index',
                'meta' => [
                    'title' => '定时任务管理',
                    'i18n' => 'mineCrontab.menu.crontabManage',
                    'icon' => 'material-symbols:alarm-outline-rounded',
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
                        'name' => 'plugin:mine-admin:crontab:list',
                        'meta' => new Meta([
                            'title' => '定时任务列表',
                            'type' => 'B',
                            'i18n' => 'mineCrontab.menu.index',
                        ]),
                    ],
                    [
                        'name' => 'plugin:mine-admin:crontab:create',
                        'meta' => new Meta([
                            'title' => '定时任务新增',
                            'type' => 'B',
                            'i18n' => 'mineCrontab.menu.save',
                        ]),
                    ],
                    [
                        'name' => 'plugin:mine-admin:crontab:save',
                        'meta' => new Meta([
                            'title' => '定时任务更新',
                            'type' => 'B',
                            'i18n' => 'mineCrontab.menu.update',
                        ]),
                    ],
                    [
                        'name' => 'plugin:mine-admin:crontab:save:delete',
                        'meta' => new Meta([
                            'title' => '定时任务删除',
                            'type' => 'B',
                            'i18n' => 'mineCrontab.menu.delete',
                        ]),
                    ],
                    [
                        'name' => 'plugin:mine-admin:crontab:execute',
                        'meta' => new Meta([
                            'title' => '定时任务执行',
                            'type' => 'B',
                            'i18n' => 'mineCrontab.menu.execute',
                        ]),
                    ],
                ],
            ],
        ];

        $this->create($menu);
    }

    public function create(array $data, int $parent_id = 0): void
    {
        //        foreach ($data as $v) {
        //            $_v = $v;
        //            if (isset($v['children'])) {
        //                unset($_v['children']);
        //            }
        //            $_v['parent_id'] = $parent_id;
        //            $menu = Menu::create(array_merge(self::BASE_MENU_DATA, $_v));
        //            if (isset($v['children']) && count($v['children'])) {
        //                $this->create($v['children'], $menu->id);
        //            }
        //        }

        foreach ($data as $v) {
            $_v = $v;
            if (isset($v['children'])) {
                unset($_v['children']);
            }
            $_v['parent_id'] = $parent_id;

            // 判断菜单是否已存在
            $menu = Menu::where('name', $_v['name'])->where('parent_id', $parent_id)->first();

            if (! $menu) {
                // 不存在则创建
                $menu = Menu::create(array_merge(self::BASE_MENU_DATA, $_v));
            }

            // 如果有子菜单，递归创建
            if (isset($v['children']) && \count($v['children'])) {
                $this->create($v['children'], $menu->id);
            }
        }
    }
}
