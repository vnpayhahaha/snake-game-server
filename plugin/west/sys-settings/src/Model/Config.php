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

namespace Plugin\West\SysSettings\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\DbConnection\Model\Model as MineModel;

/**
 * Class system_setting_config.
 * @property int $group_id
 * @property string $key
 * @property string $value
 * @property string $name
 * @property string $input_type
 * @property array $config_select_data
 * @property int $sort
 * @property string $remark
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class Config extends MineModel
{
    protected ?string $table = 'system_setting_config';

    protected string $primaryKey = 'key';

    protected array $fillable = [
        'group_id',
        'key',
        'value',
        'name',
        'input_type',
        'config_select_data',
        'sort',
        'remark', 'created_at', 'updated_at', 'created_by', 'updated_by',
    ];

    protected array $casts = [
        'group_id' => 'integer',
        'key' => 'string',
        'value' => 'string',
        'name' => 'string',
        'input_type' => 'string',
        'config_select_data' => 'array',
        'sort' => 'integer',
        'remark' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    // 反向关联配置组
    public function group(): BelongsTo
    {
        return $this->belongsTo(ConfigGroup::class, 'group_id');
    }
}
