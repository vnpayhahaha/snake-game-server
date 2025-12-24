<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import type { TenantVo } from '$/mine-admin/tenant/api/tenant'
import { create, save } from '$/mine-admin/tenant/api/tenant'
import getFormItems from './components/getFormItems.tsx'
import type { MaFormExpose } from '@mineadmin/form'
import useForm from '@/hooks/useForm.ts'
import { ResultCode } from '@/utils/ResultCode.ts'

defineOptions({ name: 'plugin:mine-admin:tenant:form' })

const { formType = 'add', data = null } = defineProps<{
  formType?: 'add' | 'edit'
  data?: TenantVo | null
}>()

const t = useTrans().globalTrans
const tenantForm = ref<MaFormExpose>()
const tenantModel = ref<TenantVo>({})

useForm('tenantForm').then((form: MaFormExpose) => {
  if (formType === 'edit' && data) {
    Object.keys(data).map((key: string) => {
      tenantModel.value[key] = data[key]
    })
  }
  form.setItems(getFormItems(formType, t, tenantModel.value))
  form.setOptions({
    labelWidth: '105px',
  })
})

// 创建操作
function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    create(tenantModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

// 更新操作
function edit(): Promise<any> {
  return new Promise((resolve, reject) => {
    save(tenantModel.value.id as number, tenantModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

defineExpose({
  add,
  edit,
  maForm: tenantForm,
})
</script>

<template>
  <ma-form ref="tenantForm" v-model="tenantModel" />
</template>

<style scoped lang="scss">

</style>
