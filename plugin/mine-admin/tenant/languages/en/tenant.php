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
    'name' => 'Tenant Name',
    'package_id' => 'Package Code',
    'account_count' => 'Maximum Account Quota',
    'contact_name' => 'Contact Person Name',
    'contact_phone' => 'Contact Phone Number',
    'expire_at' => 'Expiration Time',
    'bind_domain' => 'Bound Domain',
    'package' => [
        'name' => 'Package Name',
        'menus' => 'Permission Menus',
    ],
    'common' => [
        'remark' => 'Remarks',
        'status' => 'Status',
    ],
    'enums' => [
        'tenant_status' => [
            1 => 'Tenant Normal',
            2 => 'Tenant Disabled',
        ],
        'package_status' => [
            1 => 'Tenant Package Normal',
            2 => 'Tenant Package Disabled',
        ]
    ],
    'max_account_count' => 'Unable to create account, maximum account quota has been reached',
    'user_not_found' => 'User not found under the tenant',
    'miss_tenant_id' => 'Missing required parameter: tenant_id',
    'package_expire' => 'The package being used has expired',
];
