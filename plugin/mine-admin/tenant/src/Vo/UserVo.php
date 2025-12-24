<?php

namespace Plugin\MineAdmin\Tenant\Vo;

use App\Model\Enums\User\Status;
use App\Model\Enums\User\Type;
use App\Model\Permission\Role;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Plugin\MineAdmin\Tenant\Model\Tenant;

/**
 * @property int $id 用户ID，主键
 * @property Tenant $tenant 租户信息
 * @property string $username 用户名
 * @property Type $user_type 用户类型：(100系统用户)
 * @property string $nickname 用户昵称
 * @property string $phone 手机
 * @property string $email 用户邮箱
 * @property string $avatar 用户头像
 * @property string $signed 个人签名
 * @property Status $status 状态 (1正常 2停用)
 * @property string $login_ip 最后登陆IP
 * @property string $login_time 最后登陆时间
 * @property array $backend_setting 后台设置数据
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $remark 备注
 * @property null|Collection|Role[] $roles
 * @property mixed $password 密码
 */
class UserVo
{

}