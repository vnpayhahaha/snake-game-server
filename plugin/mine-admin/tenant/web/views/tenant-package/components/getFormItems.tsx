/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MaFormItem } from '@mineadmin/form'
import type { TenantPackageVo } from '$/mine-admin/tenant/api/tenantPackage'
import MaDictRadio from '@/components/ma-dict-picker/ma-dict-radio.vue'
import MaTree from '@/components/ma-tree/index.vue'

export default function getFormItems(formType: 'add' | 'edit' = 'add', t: any, model: TenantPackageVo): MaFormItem[] {
  if (formType === 'add') {
    model.status = 1
    model.menus = []
  }

  return [
    {
      label: () => '套餐名称',
      prop: 'package_name',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: '套餐名称' }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: '套餐名称' }) }],
      },
    },
    {
      label: () => '租户菜单',
      prop: 'menus',
      hide: () => model.id === 1,
    },
    {
      label: () => t('crud.remark'),
      prop: 'remark',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('crud.remark') }),
        type: 'textarea',
      },
    },
    {
      label: () => t('crud.status'),
      prop: 'status',
      render: () => MaDictRadio,
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('crud.status') }),
        dictName: 'system-status',
      },
    },
  ]
}
