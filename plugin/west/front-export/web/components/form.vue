<script setup lang="ts">
import useForm from '@/hooks/useForm.ts'
import type { MaFormExpose } from '@mineadmin/form'
import getFormItems from '../data/getFormItems.tsx'
import { extractLabels } from '$/west/front-export/utils/tools.ts'

const { data = null, maDialog = null } = defineProps<{
  data?: any | null
  maDialog?: any
}>()

const t = useTrans().globalTrans
const maFormRef = ref<MaFormExpose>()
const formModel = ref<any>({})
const form = ref<MaFormExpose | null>(null)
const columns = reactive(extractLabels(inject('columns')) ?? [])
const options = reactive(inject('options') ?? {})
// 初始化表单数据
useForm('maFormRef').then((loadedForm: MaFormExpose) => {
  form.value = loadedForm
  // 设置表单项
  form.value.setItems(getFormItems(t, formModel.value, { maDialog }, options))
  form.value.setOptions({
    labelPosition: 'top',
  })
})

// 监听 formModel.value.type 变化
watch(
  () => formModel.value.type, // 只监听 `type` 字段
  (newType, oldType) => {
    formModel.value.fields = newType === 'STANDARD' ? columns : []
  },
)

// 监听 data 数据变化
watch(
  () => data.fields,
  (newData, oldData) => {
    formModel.value.fields = newData
  },
)

function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    resolve(formModel.value) // 正常返回字段数据
  })
}

defineExpose({
  add,
  maForm: maFormRef,
})
</script>

<template>
  <div class="relative w-full overflow-hidden p-6">
    <ma-form ref="maFormRef" v-model="formModel" />
  </div>
</template>

<style scoped lang="scss">
:deep(.el-form-item__label){
  width: 100%;
}
</style>
