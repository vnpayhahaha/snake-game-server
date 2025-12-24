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
use Hyperf\Database\Model\Builder;
use Plugin\West\SysSettings\Model\Config as Model;

/**
 * 参数配置表 Repository类.
 */
class ConfigRepository extends IRepository
{
    public function __construct(
        protected readonly Model $model
    ) {}

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['group_id'])) {
            $query->where('group_id', '=', $params['group_id']);
        }

        if (isset($params['key'])) {
            $query->where('key', '=', $params['key']);
        }

        if (isset($params['value'])) {
            $query->where('value', '=', $params['value']);
        }

        if (isset($params['name'])) {
            $query->where('name', '=', $params['name']);
        }

        if (isset($params['input_type'])) {
            $query->where('input_type', '=', $params['input_type']);
        }

        if (isset($params['config_select_data'])) {
            $query->where('config_select_data', '=', $params['config_select_data']);
        }

        if (isset($params['sort'])) {
            $query->where('sort', '=', $params['sort']);
        }

        if (isset($params['remark'])) {
            $query->where('remark', '=', $params['remark']);
        }

        return $query;
    }
}
