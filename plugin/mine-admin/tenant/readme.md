## Mine Tenant 多租户插件

本插件由官方开发，兼容 `master` 和 `master-department` 分支，功能都是无侵入源码式附加，可以安心食用。

> 当前最新版本：1.1.0

## 什么是多租户

多租户，简单来说是指一个业务系统，可以为多个组织服务，并且组织之间的数据是隔离的。
例如说，在服务上部署了 `MineAdmin` 系统，并安装了多租户插件，就可以支持多个不同的公司使用。这里的一个公司就是一个租户，
每个用户必然属于某个租户。因此，用户也只能看见自己租户下面的内容，其它租户的内容对他是不可见的。

## 隔离方案

我们采用了最简单以及最便捷的`【字段隔离】`方案

## 下载和安装插件

1. 下载
```shell
php bin/hyperf.php mine-extension:download mine-admin/tenant
```

2. 安装
```shell
php bin/hyperf.php mine-extension:install mine-admin/tenant --yes
```

> 注意：安装时，会询问使用的哪个分支，如果是部门分支请输入 `yes`

## 卸载插件
插件卸载并不会

```shell
php bin/hyperf.php mine-extension:uninstall mine-admin/tenant --yes
```

## 后端获取上下文

提供了 `Plugin\MineAdmin\Tenant\Utils\TenantUtils::class` 类来获取上下文数据

```php
use \Plugin\MineAdmin\Tenant\Utils\TenantUtils

// 获取当前租户ID
$id = TenantUtils::getTenantId();

// 获取当前用户，其实是 CurrentUser 类
$user_id = TenantUtils::getCurrentUser()->id();

// 检查是否为默认租户
if (TenantUtils::isDefaultTenant()) {
    // todo...
}

// 检查当前用户是否为租户超管
if (TenantUtils::isTenantManage()) {
    // todo...
}
```

## 前端获取当前租户ID
```ts
import useCache from '@/hooks/useCache.ts'

$tenant_id = useCache('tenant')
```

## TenantIgnore 注解

声明 `#[TenantIgnore]` 到类、或者方法上，则标记指定类或方法不进行租户过滤查询，就是避免系统自动拼接
`WHERE tenant_id = ?` 条件。

## 注意事项

### 域名相关
- 租户绑定域名，请不需要加域名协议和尾部的 `/`，正确例子： `example.mineadmin.com`
- 绑定后，通过 `header` 传入的 `tenant_id` 会失效，将全部通过域名来识别。

### 其他事项

- 暂无

## 后台租户切换

当前 `1.0.0` 版暂时没有开发，后续版本将加入