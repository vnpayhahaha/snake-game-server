/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MaProTableColumns, MaProTableExpose } from '@mineadmin/pro-table'
import type { TenantVo } from '$/mine-admin/tenant/api/tenant'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import { useMessage } from '@/hooks/useMessage.ts'
import { deleteByIds } from '$/mine-admin/tenant/api/tenant'
import { ResultCode } from '@/utils/ResultCode.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'

export default function getTableColumns(dialog: UseDialogExpose, formRef: any, t: any): MaProTableColumns[] {
  const dictStore = useDictStore()
  const msg = useMessage()

  const showBtn = (auth: string | string[], row: TenantVo) => {
    return hasAuth(auth) && row.id !== 1
  }

  return [
    // 多选列
    { type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection'),
      cellRender: ({ row }): any => row.id === 1 ? '-' : undefined,
      selectable: (row: TenantVo) => ![1].includes(row.id as number),
    },
    // 普通列
    { label: () => '租户ID', prop: 'id' },
    { label: () => '租户名', prop: 'name' },
    { label: () => '所属套餐', prop: 'package.package_name',
      cellRender: ({ row }) => (
        <el-popover
          title="套餐详情"
          placement="right"
          width="300"
        >
          {{
            reference: () => <el-link type="primary" underline="never">{row?.package?.package_name}</el-link>,
            default: () => {
              return (
                <el-descriptions border column={1} class="w-full" size="small">
                  <el-descriptions-item label="套餐名称">{row?.package?.package_name}</el-descriptions-item>
                  <el-descriptions-item label="套餐描述">{row?.package?.remark}</el-descriptions-item>
                </el-descriptions>
              )
            },
          }}
        </el-popover>
      ),
    },
    {
      label: () => '租户管理员', prop: 'user.username',
      cellRender: ({ row }) => (
        <el-popover
          title="管理员详情"
          placement="right"
          width="300"
        >
          {{
            reference: () => <el-link type="primary" underline="never">{row?.user?.username}</el-link>,
            default: () => {
              return (
                <el-descriptions border column={1} class="w-full" size="small">
                  <el-descriptions-item label="用户名">{row?.user?.username}</el-descriptions-item>
                  <el-descriptions-item label="昵称">{row?.user?.nickname}</el-descriptions-item>
                  <el-descriptions-item label="电话">{row?.user?.phone}</el-descriptions-item>
                  <el-descriptions-item label="邮箱">{row?.user?.email}</el-descriptions-item>
                  <el-descriptions-item label="最后登录时间">{row?.user?.login_time}</el-descriptions-item>
                </el-descriptions>
              )
            },
          }}
        </el-popover>
      ),
    },
    { label: () => '联系人', prop: 'contact_name' },
    { label: () => '联系电话', prop: 'contact_phone' },
    { label: () => '账号配额（个）', prop: 'account_count' },
    { label: () => '状态', prop: 'status',
      cellRender: ({ row }) => (
        <el-tag type={dictStore.t('system-status', row.status, 'color')}>
          {t(dictStore.t('system-status', row.status, 'i18n'))}
        </el-tag>
      ),
    },
    { label: () => '过期时间', prop: 'expire_at', cellRender: ({ row }) => row.expire_at.substring(0, 10) },
    // 操作列
    {
      type: 'operation',
      label: () => t('crud.operation'),
      operationConfigure: {
        type: 'tile',
        actions: [
          {
            name: 'edit',
            icon: 'material-symbols:person-edit',
            text: () => t('crud.edit'),
            onClick: ({ row }) => {
              dialog.setTitle(t('crud.edit'))
              dialog.open({ formType: 'edit', data: row })
            },
          },
          {
            name: 'del',
            show: ({ row }) => showBtn('plugin:mine-admin:tenant:delete', row),
            icon: 'mdi:delete',
            text: () => t('crud.delete'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('crud.delDataMessage')).then(async () => {
                const response = await deleteByIds([row.id])
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('crud.delSuccess'))
                  await proxy.refresh()
                }
              })
            },
          },
        ],
      },
    },
  ]
}
