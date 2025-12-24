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
import type { ConfigVo } from '$/west/sys-settings/api/config.ts'
import MaRemoteSelect from '@/components/ma-remote-select/index.vue'
import MaKeyValue from '@/components/ma-key-value/index.vue'

import { page } from '$/west/sys-settings/api/configGroup.ts'
import MaDictSelect from '@/components/ma-dict-picker/ma-dict-select.vue'

export default function getFormItems(formType: 'add' | 'edit' = 'add', t: any, model: ConfigVo): MaFormItem[] {
  const isDataSourceHidden = ref(true) // 控制数据源显示与否
  if (formType === 'add') {
    model.sort = 0
    model.input_type = 'input'
  }

  // 如果数据源显示
  if (formType === 'add' && isDataSourceHidden.value) {
    model.config_select_data = []
  }

  return [
    {
      label: () => t('systemMenu.systemSetting.field.belongingGroup'),
      prop: 'group_id',
      render: () => MaRemoteSelect,
      renderProps: {
        disabled: true,
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.belongingGroup') }),
        api: () => new Promise(resolve => resolve(page({ page_size: 999 }))),
        dataHandle: (response: any) => {
          return response.data?.map((item: ConfigGroupVo) => {
            return { label: `${item.name}`, value: item.id }
          })
        },
      },
      itemProps: {
        rules: [{ required: true, message: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.belongingGroup') }) }],
      },
    },
    {
      label: () => t('systemMenu.systemSetting.field.configTitle'),
      prop: 'name',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.configTitle') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.configTitle') }) }],
      },
    },
    {
      label: () => t('systemMenu.systemSetting.field.configIdentifier'),
      prop: 'key',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.configIdentifier') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.configIdentifier') }) }],
      },
    },
    {
      label: () => t('systemMenu.systemSetting.field.configValue'),
      prop: 'value',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.configValue') }),
      },
    },
    {
      label: () => t('systemMenu.systemSetting.field.sortOrder'),
      prop: 'sort',
      render: 'inputNumber',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.sortOrder') }),
      },
    },
    {
      label: () => t('systemMenu.systemSetting.field.component'),
      prop: 'input_type',
      render: () => MaDictSelect,
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.component') }),
        data: () => {
          // 在 dataHandle 中定义一个数组
          return [
            { label: t('systemMenu.systemSetting.field.componentInfo.textBox'), value: 'input' },
            { label: t('systemMenu.systemSetting.field.componentInfo.textArea'), value: 'textarea' },
            { label: t('systemMenu.systemSetting.field.componentInfo.dropdown'), value: 'select' },
            { label: t('systemMenu.systemSetting.field.componentInfo.radio'), value: 'radio' },
            { label: t('systemMenu.systemSetting.field.componentInfo.checkbox'), value: 'checkbox' },
            { label: t('systemMenu.systemSetting.field.componentInfo.switch'), value: 'switch' },
            { label: t('systemMenu.systemSetting.field.componentInfo.imageUpload'), value: 'imageUpload' },
            { label: t('systemMenu.systemSetting.field.componentInfo.keyValuePair'), value: 'keyValuePair' },
            { label: t('systemMenu.systemSetting.field.componentInfo.tinyMCE'), value: 'tinyMCE' },
          ]
        },
        onChange: (value: string) => {
          isDataSourceHidden.value = !['select', 'radio', 'checkbox', 'switch', 'keyValuePair'].includes(value)
        },
      },
    },
    {
      label: () => t('systemMenu.systemSetting.field.dataSource'),
      prop: 'config_select_data',
      hide: () => isDataSourceHidden.value,
      render: () => MaKeyValue,
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.dataSource') }),
      },
    },
    {
      label: () => t('systemMenu.systemSetting.field.configDescription'),
      prop: 'remark',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('systemMenu.systemSetting.field.configDescription') }),
      },
    },
  ]
}
