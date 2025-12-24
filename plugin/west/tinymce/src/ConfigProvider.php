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

namespace Plugin\West\Tinymce;

class ConfigProvider
{
    public function __invoke()
    {
        // Initial configuration
        return [
            // 合并到  config/autoload/annotations.php 文件
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            // 组件默认配置文件，即执行命令后会把 source 的对应的文件复制为 destination 对应的的文件
            'publish' => [
                [
                    'id' => 'web:tinymce-style',
                    'description' => '迁移 TinyMCE 自定义皮肤样式文件到前端 public 目录',
                    'source' => __DIR__ . '/../publish/', // 插件中的源目录
                    'destination' => BASE_PATH . '/web/public/tinymce', // 目标 public 目录
                ],
            ],
        ];
    }
}
