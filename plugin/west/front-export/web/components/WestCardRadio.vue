<script setup lang="ts">
defineOptions({ name: 'WestCardRadio' })

const props = defineProps<{
  modelModifiers?: object
  data?: Array<any>
  modelValue?: any
}>()
const model = defineModel<any>()

const t = useTrans().globalTrans
</script>

<template>
  <el-radio-group v-model="model" class="gap-2.5">
    <template v-for="(item, index) in props.data">
      <el-radio :value="item.value" :disabled="item.disabled">
        <template #default>
          <div class="oc-radio-box w-250px flex flex-col cursor-pointer border border-[#dcdde0] rounded-[4px] border-solid p-[12px] !dark:bg-dark-6">
            <div class="flex items-center gap-2.5">
              <img v-if="item.icon" :src="item.icon" alt="">
              <span>{{ t(item.label) }}</span>
            </div>
            <el-text class="self-start !line-height-normal" size="small" type="info">
              {{ t(item.describe) }}
            </el-text>
          </div>
        </template>
      </el-radio>
    </template>
  </el-radio-group>
</template>

<style scoped lang="scss">
:deep(.el-form-item__content) {
  line-height: unset;
}

:deep(.el-radio__input) {
  display: none;
}

:deep(.el-radio) {
  color: var(--el-text-color-primary);
  height: unset;
  white-space: unset;
  margin-right: 0;
}

:deep(.el-radio__label) {
  padding: 0 !important;
}

:deep(.dark .is-checked .oc-radio-box){
  background: #181818 !important;
}

:deep(.oc-radio-box) {
  --el-bg-color: #f7f8fa;
  width: 250px;
  border-radius: 4px;
  background: var(--el-bg-color);
  border-color: white;
  border-width: 1px;
  border-style: solid;
}
:deep(.dark .oc-radio-box) {
  background: rgb(28 28 30 / var(--un-bg-opacity)) /* #1c1c1e */;
}
:deep(.is-checked .oc-radio-box) {
  background: #ffffff;
  border-color: rgb(var(--ui-primary));
  border-width: 1px;
  border-style: solid;
}
</style>
