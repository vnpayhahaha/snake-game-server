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
return [
    'name' => ' 租户名称',
    'package_id' => '套餐编码',
    'account_count' => '账号最大配额',
    'contact_name' => '联系人姓名',
    'contact_phone' => '联系人电话',
    'expire_at' => '过期时间',
    'bind_domain' => '绑定域名',
    'package' => [
        'name' => '套餐名称',
        'menus' => '权限菜单',
    ],
    'common' => [
        'remark' => '备注',
        'status' => '状态',
    ],
    'enums' => [
        'tenant_status' => [
            1 => '租户正常',
            2 => '租户被停用',
        ],
        'package_status' => [
            1 => '租户套餐正常',
            2 => '租户套餐被停用',
        ]
    ],
    'max_account_count' => '无法创建账号，已达到最大账号配额数',
    'user_not_found' => '找不到租户下的用户',
    'miss_tenant_id' => '缺少携带参数：tenant_id',
    'package_expire' => '使用的套餐已过期',
];
