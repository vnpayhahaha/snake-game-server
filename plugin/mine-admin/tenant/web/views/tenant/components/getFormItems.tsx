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
import type { TenantVo } from '$/mine-admin/tenant/api/tenant'
import { page as packagePage } from '$/mine-admin/tenant/api/tenantPackage'
import MaDictRadio from '@/components/ma-dict-picker/ma-dict-radio.vue'

export default function getFormItems(formType: 'add' | 'edit' = 'add', t: any, model: TenantVo): MaFormItem[] {
  if (formType === 'add') {
    model.status = 1
    model.account_count = 10
    model.expire_at = useDayjs().add(1, 'year').format('YYYY-MM-DD')
    model.contact_phone = ''
  }

  if (!model.bind_domain) {
    model.bind_domain = ''
  }

  return [
    {
      label: () => '租户名称',
      prop: 'name',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: '租户名称' }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: '租户名称' }) }],
      },
    },
    {
      label: () => '租户套餐',
      prop: 'package_id',
      render: () => <ma-remote-select />,
      cols: { md: 12, xs: 24 },
      renderProps: {
        api: packagePage,
        axiosConfig: { page_size: 999 },
        dataHandle: (response: any) => {
          return response.data?.list.map((item: any) => {
            return { label: item.package_name, value: item.id }
          })
        },
        placeholder: t('form.pleaseInput', { msg: '租户套餐' }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: '租户套餐' }) }],
      },
    },
    {
      label: () => '管理员账号',
      prop: 'username',
      render: 'input',
      cols: { md: 12, xs: 24 },
      hide: formType === 'edit',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: '管理员账号' }),
      },
      itemProps: {
        rules: formType === 'add' ? [{ required: true, message: t('form.requiredInput', { msg: '管理员账号' }) }] : [],
      },
    },
    {
      label: () => '管理员密码',
      prop: 'password',
      render: 'input',
      cols: { md: 12, xs: 24 },
      hide: formType === 'edit',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: '管理员密码' }),
      },
      itemProps: {
        rules: formType === 'add' ? [{ required: true, message: t('form.requiredInput', { msg: '管理员密码' }) }] : [],
      },
    },
    {
      label: () => '联系人',
      prop: 'contact_name',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: '联系人' }),
      },
      itemProps: {
        rules: formType === 'add' ? [{ required: true, message: t('form.requiredInput', { msg: '联系人' }) }] : [],
      },
    },
    {
      label: () => '联系电话',
      prop: 'contact_phone',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: '联系电话' }),
      },
    },
    {
      label: () => '账号配额',
      prop: 'account_count',
      render: 'inputNumber',
      cols: { md: 12, xs: 24 },
      renderProps: {
        style: 'width: 100%',
        placeholder: t('form.pleaseInput', { msg: '账号配额' }),
      },
      itemProps: {
        rules: formType === 'add' ? [{ required: true, message: t('form.requiredInput', { msg: '账号配额' }) }] : [],
      },
    },
    {
      label: () => '过期时间',
      prop: 'expire_at',
      render: 'datePicker',
      cols: { md: 12, xs: 24 },
      renderProps: {
        style: 'width: 100%',
        placeholder: t('form.pleaseInput', { msg: '过期时间' }),
        format: 'YYYY-MM-DD',
        valueFormat: 'YYYY-MM-DD',
      },
      itemProps: {
        rules: formType === 'add' ? [{ required: true, message: t('form.requiredInput', { msg: '过期时间' }) }] : [],
      },
    },
    {
      label: () => '绑定域名',
      prop: 'bind_domain',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: '绑定域名' }),
      },
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
