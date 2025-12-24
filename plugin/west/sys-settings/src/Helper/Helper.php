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

namespace Plugin\West\SysSettings\Helper;

use Plugin\West\SysSettings\Repository\ConfigGroupRepository;
use Plugin\West\SysSettings\Repository\ConfigRepository;

class Helper
{
    /**
     * 获取所有分组数据.
     */
    public static function getSysSettingType(?string $code = null): mixed
    {
        $repository = make(ConfigGroupRepository::class);
        if ($code === null) {
            return $repository->list(['created_at' => 'desc']);
        }
        return $repository->findByFilter(['code' => $code, 'created_at' => 'desc']);
    }

    /**
     * 获取某个配置数据.
     */
    public static function getSysSettingByTypeCode(string $typeCode): mixed
    {
        return make(ConfigRepository::class)
            ->getQuery()
            ->where('key', $typeCode)
            ->orderByDesc('created_at')
            ->first();
    }
}
