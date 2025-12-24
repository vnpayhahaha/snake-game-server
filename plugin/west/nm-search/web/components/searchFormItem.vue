<script setup lang="ts">
import { getComponentByRender, resolveLabel } from '$/west/nm-search/utils/tools.ts'

defineOptions({ name: 'SearchFormItem' })

const props = defineProps<{
  item: any
  hideLabel?: boolean
  render?: ((modelValue: any) => any) | null
}>()

const modelValue = defineModel<any>()
</script>

<template>
  <el-form-item
    :prop="item.prop"
    :label="props.hideLabel ? undefined : resolveLabel(props.item.label)"
    class="nm-search-form-item"
  >
    <!-- 根据 render 或 item.render 渲染组件 -->
    <component
      :is="getComponentByRender(props.item.render)"
      v-model="modelValue"
      :name="resolveLabel(props.item.label)"
      v-bind="props.item.renderProps"
    />
  </el-form-item>
</template>
