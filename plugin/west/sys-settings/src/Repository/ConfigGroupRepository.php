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

namespace Plugin\West\SysSettings\Repository;

use App\Repository\IRepository;
use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Builder;
use Plugin\West\SysSettings\Model\ConfigGroup as Model;

/**
 * 参数配置分组表 Repository类.
 */
class ConfigGroupRepository extends IRepository
{
    public function __construct(
        protected readonly Model $model
    ) {}

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->with('info');

        // 主键
        if (isset($params['id'])) {
            $query->where('id', '=', $params['id']);
        }

        // 配置组名称
        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        // 配置组标识
        if (isset($params['code'])) {
            $query->where('code', 'like', '%' . $params['code'] . '%');
        }

        // 配置组图标
        if (isset($params['icon'])) {
            $query->where('icon', 'like', '%' . $params['icon'] . '%');
        }

        // 创建者
        if (isset($params['created_by'])) {
            $query->where('created_by', '=', $params['created_by']);
        }

        // 更新者
        if (isset($params['updated_by'])) {
            $query->where('updated_by', '=', $params['updated_by']);
        }

        // 创建时间
        if (isset($params['created_at']) && \is_array($params['created_at']) && \count($params['created_at']) === 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0], $params['created_at'][1]]
            );
        }

        // 更新时间
        if (isset($params['updated_at']) && \is_array($params['updated_at']) && \count($params['updated_at']) === 2) {
            $query->whereBetween(
                'updated_at',
                [$params['updated_at'][0], $params['updated_at'][1]]
            );
        }

        // 备注
        if (isset($params['remark'])) {
            $query->where('remark', 'like', '%' . $params['remark'] . '%');
        }

        return $query;
    }
}
