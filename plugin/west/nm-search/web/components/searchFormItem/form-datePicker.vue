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
  <el-date-picker
    v-model="props.modelValue"
    :placeholder="`请选择${props.name}`"
    @update:model-value="updateValue"
  />
</template>
