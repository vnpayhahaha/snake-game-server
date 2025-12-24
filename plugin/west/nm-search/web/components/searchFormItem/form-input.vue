<script setup lang="ts">
import { defineEmits, defineProps } from 'vue'

const props = defineProps({
  modelValue: { type: String, required: true },
  name: { type: String, required: true },
})
const emit = defineEmits(['update:modelValue'])

function updateValue(value: string) {
  emit('update:modelValue', value)
}
</script>

<template>
  <el-input
    v-model="props.modelValue"
    :placeholder="`请输入${props.name}`"
    clearable
    @update:model-value="updateValue"
  >
    <template #prefix>
      <div>{{ props.name }}</div>
    </template>
  </el-input>
</template>
