<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
import type { TenantPackageVo } from '$/mine-admin/tenant/api/tenantPackage'
import { create, save } from '$/mine-admin/tenant/api/tenantPackage'
import { list } from '$/mine-admin/tenant/api/menu'
import getFormItems from './components/getFormItems.tsx'
import type { MaFormExpose } from '@mineadmin/form'
import useForm from '@/hooks/useForm.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import MaTree from '@/components/ma-tree/index.vue'

defineOptions({ name: 'plugin:mine-admin:tenant-package:form' })

const { formType = 'add', data = null } = defineProps<{
  formType?: 'add' | 'edit'
  data?: TenantPackageVo | null
}>()

const t = useTrans().globalTrans
const tenantPackageForm = ref<MaFormExpose>()
const tenantPackageModel = ref<TenantPackageVo>({})
const permissionTreeRef = ref()
const checkStrictly = ref<boolean>(false)
const menus = inject('menus')

useForm('tenantPackageForm').then(async (form: MaFormExpose) => {
  const names: string[] = []
  if (formType === 'edit' && data) {
    Object.keys(data).map((key: string) => {
      tenantPackageModel.value[key] = data[key]
    })
    tenantPackageModel.value.menus?.map((item: any) => {
      names.push(item.name)
    })
    checkStrictly.value = true
  }
  form.setItems(getFormItems(formType, t, tenantPackageModel.value))
  const item = form.getItemByProp('menus')
  item.render = () => MaTree
  item.renderProps = {
    ref: (el: any) => permissionTreeRef.value = el,
    class: 'w-full',
    showCheckbox: true,
    treeKey: 'meta.title',
    placeholder: t('form.pleaseSelect', { msg: '租户菜单' }),
    nodeKey: 'name',
    data: menus,
  }
  item.renderSlots = {
    default: ({ data }) => {
      return (
        <div class="mine-tree-node">
          <div class="label">
            { data.meta?.icon && <ma-svg-icon name={data.meta?.icon} size={16} />}
            { data.meta?.i18n ? t(data.meta?.i18n) : data.meta.title ?? 'unknown' }
          </div>
        </div>
      )
    },
  }

  form.setOptions({
    labelWidth: '105px',
  })

  await nextTick(() => {
    permissionTreeRef.value?.setCheckStrictly(!!tenantPackageModel.value?.id)
    setTimeout(() => {
      permissionTreeRef.value?.elTree?.setCheckedKeys?.(names)
    }, 50)
  })
})

// 创建操作
function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    const elTree = permissionTreeRef.value.elTree
    tenantPackageModel.value.menus = elTree.getCheckedKeys()
    create(tenantPackageModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

// 更新操作
function edit(): Promise<any> {
  return new Promise((resolve, reject) => {
    const elTree = permissionTreeRef.value.elTree
    tenantPackageModel.value.menus = elTree.getCheckedKeys()
    save(tenantPackageModel.value.id as number, tenantPackageModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

defineExpose({
  add,
  edit,
  maForm: tenantPackageForm,
})
</script>

<template>
  <ma-form ref="tenantPackageForm" v-model="tenantPackageModel" />
</template>

<style scoped lang="scss">

</style>
