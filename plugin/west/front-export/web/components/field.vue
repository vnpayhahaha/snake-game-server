<script setup lang="ts">
import type { MaFormExpose } from '@mineadmin/form'
import useForm from '@/hooks/useForm.ts'
import getFieldItems from '$/west/front-export/data/getFieldItems.tsx'
import { extractLabels } from '$/west/front-export/utils/tools.ts'

const t = useTrans().globalTrans
const maFieldRef = ref<MaFormExpose>()
const fieldModel = ref<any>({})
const columns = reactive(extractLabels(inject('columns')) ?? [])

useForm('maFieldRef').then((form: MaFormExpose) => {
  form.setItems(getFieldItems(columns, t, fieldModel.value))
  form.setOptions({
    labelWidth: '80px',
    labelPosition: 'top',
  })
})

function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    if (!fieldModel.value.fields || fieldModel.value.fields.length === 0) {
      reject(new Error(t('frontExport.selectField')))
      return
    }
    resolve(fieldModel.value.fields) // 正常返回字段数据
  })
}

defineExpose({
  add,
  maField: maFieldRef,
})
</script>

<template>
  <ma-form ref="maFieldRef" v-model="fieldModel" />
</template>

<style scoped lang="scss">

</style>
