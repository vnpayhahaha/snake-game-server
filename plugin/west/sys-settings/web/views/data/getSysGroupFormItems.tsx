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
import type { ConfigGroupVo } from '$/west/sys-settings/api/configGroup.ts'
import MaIconPicker from '@/components/ma-icon-picker/index.vue'

export default function getSysGroupFormItems(formType: 'add' | 'edit' = 'add', t: any, model: ConfigGroupVo): MaFormItem[] {
  return [
    {
      label: () => t('systemMenu.systemSetting.field.groupName'),
      prop: 'name',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.groupName') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: t('systemMenu.systemSetting.field.groupName') }) }],
      },
    },
    {
      label: () => t('systemMenu.systemSetting.field.groupIdentifier'),
      prop: 'code',
      render: 'input',
      renderProps: {
        disabled: formType === 'edit',
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.groupIdentifier') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: t('systemMenu.systemSetting.field.groupIdentifier') }) }],
      },
    },
    {
      label: () => t('systemMenu.systemSetting.field.groupIcon'),
      prop: 'icon',
      render: () => MaIconPicker,
      renderProps: {
        class: 'w-full',
      },
    },
    {
      label: () => t('systemMenu.systemSetting.field.itemRemark'),
      prop: 'remark',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.itemRemark') }),
        type: 'textarea',
      },
    },
  ]
}
