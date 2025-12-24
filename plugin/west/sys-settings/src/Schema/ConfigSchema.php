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

namespace Plugin\West\SysSettings\Schema;


use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;
use Plugin\West\SysSettings\Model\Config;

/**
 * 参数配置表.
 */
#[Schema(title: 'ConfigSchema')]
class ConfigSchema implements \JsonSerializable
{
    #[Property(property: 'group_id', title: '组id', type: 'bigint')]
    public int $group_id;

    #[Property(property: 'key', title: '配置键名', type: 'varchar')]
    public string $key;

    #[Property(property: 'value', title: '配置值', type: 'varchar')]
    public string $value;

    #[Property(property: 'name', title: '配置名称', type: 'varchar')]
    public string $name;

    #[Property(property: 'input_type', title: '数据输入类型', type: 'varchar')]
    public string $input_type;

    #[Property(property: 'config_select_data', title: '配置选项数据', type: 'varchar')]
    public array $config_select_data;

    #[Property(property: 'sort', title: '排序', type: 'smallint')]
    public int $sort;

    #[Property(property: 'remark', title: '备注', type: 'varchar')]
    public string $remark;

    public function __construct(Config $model)
    {
        $this->group_id = $model->group_id;
        $this->key = $model->key;
        $this->value = $model->value;
        $this->name = $model->name;
        $this->input_type = $model->input_type;
        $this->config_select_data = $model->config_select_data;
        $this->sort = $model->sort;
        $this->remark = $model->remark;
    }

    public function jsonSerialize(): array
    {
        return [
            'group_id' => $this->group_id,
            'key' => $this->key,
            'value' => $this->value,
            'name' => $this->name,
            'input_type' => $this->input_type,
            'config_select_data' => $this->config_select_data,
            'sort' => $this->sort,
            'remark' => $this->remark,
        ];
    }
}
