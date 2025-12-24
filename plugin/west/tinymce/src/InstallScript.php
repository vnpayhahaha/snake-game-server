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

class InstallScript
{
    public function __construct() {}

    public function __invoke(): void
    {
        echo "正在安装插件...\n";
    }

    public function arePackagesInstalled(): bool
    {
        // 检查 tinymce 和 @tinymce/tinymce-vue 是否都已安装
        return $this->isPackageInstalled('tinymce') && $this->isPackageInstalled('@tinymce/tinymce-vue');
    }

    private function isPackageInstalled(string $packageName): bool
    {
        // 检查 node_modules 中是否存在该包
        $packagePath = BASE_PATH . '/web/node_modules/' . $packageName;

        // 返回检查结果，存在则返回 true
        return file_exists($packagePath);
    }

    private function installPackage(): void
    {
        echo "正在安装 tinymce...\n";
    }
}
