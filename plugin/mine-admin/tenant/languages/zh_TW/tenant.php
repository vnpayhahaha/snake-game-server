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
    'name' => ' 租戶名稱',
    'package_id' => '方案編碼',
    'account_count' => '帳號最大配額',
    'contact_name' => '聯絡人姓名',
    'contact_phone' => '聯絡人電話',
    'expire_at' => '過期時間',
    'bind_domain' => '綁定網域',
    'package' => [
        'name' => '方案名稱',
        'menus' => '權限選單',
    ],
    'common' => [
        'remark' => '備註',
        'status' => '狀態',
    ],
    'enums' => [
        'tenant_status' => [
            1 => '租戶正常',
            2 => '租戶被停用',
        ],
        'package_status' => [
            1 => '租戶套餐正常',
            2 => '租戶套餐被停用',
        ]
    ],
    'max_account_count' => '無法創建帳號，已達到最大帳號配額數',
    'user_not_found' => '找不到租戶下的使用者',
    'miss_tenant_id' => '缺少攜帶參數：tenant_id',
    'package_expire' => '使用的套餐已過期',
];
