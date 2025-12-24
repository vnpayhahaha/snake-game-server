<script setup lang="ts">
import SearchFormItem from '$/west/nm-search/components/searchFormItem.vue'
import { getComponentByRender } from '$/west/nm-search/utils/tools'
import type { MaProTableExpose } from '@mineadmin/pro-table'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'
import type { MaSearchItem } from '@mineadmin/search'

const props = withDefaults(
  defineProps<{
    proxy?: MaProTableExpose | null
    row?: number
  }>(),
  { row: 3 },
)

const nmSearchRef = ref()
const visible = ref(false)
const searchModel = reactive<Record<string, any>>({})
const searchItem = ref<MaSearchItem[]>([])

const t = useLocalTrans()

const moreSearchItems = computed(() =>
  searchItem.value.slice(props.row).filter((item) => {
    const val = searchModel[item.prop]
    return val !== '' && val !== null && val !== undefined
  }),
)

// 提取清理逻辑到一个函数
function sanitizeSearchModel(model: Record<string, any>) {
  return Object.fromEntries(
    Object.entries(model).map(([k, v]) => [k, v !== '' && v != null ? v : null]),
  )
}

function resetSearchForm() {
  // 清空模型
  Object.keys(searchModel).forEach((key) => {
    searchModel[key] = ''
  })

  const sanitized = sanitizeSearchModel(searchModel)
  props.proxy?.setSearchForm(sanitized)
  onSearch()
}

function onSearch() {
  props.proxy?.search({})
  visible.value = false
}

watch(
  () => props.proxy,
  (proxy) => {
    if (!proxy) { return }
    const searchRef = proxy.getSearchRef()
    const items = searchRef?.getItems().filter(i => i.prop !== '__MaSearchAction') || []

    searchItem.value = items

    items.forEach((item, index) => {
      item.isMoreSearch = index >= props.row
      searchModel[item.prop] = item.value ?? ''
    })

    searchRef?.setShowState(false)
  },
  { immediate: true },
)

watch(searchModel, (model) => {
  const sanitized = sanitizeSearchModel(model)
  props.proxy?.setSearchForm(sanitized)
})
</script>

<template>
  <el-form ref="nmSearchRef" :model="searchModel" label-position="top" class="nm-search-form">
    <div class="flex flex-wrap gap-2.5">
      <!-- 主筛选项（隐藏 label） -->
      <template v-for="item in searchItem.slice(0, props.row)" :key="item.prop">
        <SearchFormItem
          v-model="searchModel[item.prop]"
          :item="item"
          :hide-label="true"
          class="nm-search-form-item-default"
          :render="getComponentByRender(item.render)"
        />
      </template>

      <!-- 更多筛选按钮和弹出层 -->
      <div>
        <el-popover :visible="visible" teleported placement="bottom-start" :width="600" trigger="click">
          <template #reference>
            <el-button text bg size="small" class="h-34px link-h-34px" @click="visible = !visible">
              <template #icon>
                <ma-svg-icon name="i-bx:filter-alt" />
              </template>
              {{ t('nmSearch.moreFilters') }}
            </el-button>
          </template>
          <el-row :gutter="24">
            <template v-for="item in searchItem.slice(props.row)" :key="item.prop">
              <el-col :span="8" class="!mb-10px">
                <SearchFormItem v-model="searchModel[item.prop]" :item="item" class="nm-search-form-item-default" />
              </el-col>
            </template>
          </el-row>
          <div class="mt-2 justify-end lg:flex">
            <el-button text @click="resetSearchForm">
              {{ t('nmSearch.clear') }}
            </el-button>
            <el-button @click="visible = false">
              {{ t('nmSearch.cancel') }}
            </el-button>
            <el-button type="primary" @click="onSearch">
              {{ t('nmSearch.confirm') }}
            </el-button>
          </div>
        </el-popover>
      </div>

      <!-- 已选中的更多筛选项（显示 label） -->
      <template v-for="item in moreSearchItems" :key="item.prop">
        <SearchFormItem
          v-model="searchModel[item.prop]"
          :item="item"
          :hide-label="true"
          class="nm-search-form-item-selected min-w-[200px]"
        />
      </template>

      <!-- 显示清空按钮 -->
      <el-button v-if="moreSearchItems.length" type="primary" text @click="resetSearchForm">
        {{ t('nmSearch.clear') }}
      </el-button>
    </div>
  </el-form>
</template>

<style scoped lang="scss">
:deep(.nm-search-form-item) {
  margin-bottom: 0 !important;
}

:deep(.nm-search-form-item .el-input__wrapper),
:deep(.nm-search-form-item .el-select__wrapper) {
  @apply bg-white dark:bg-dark-8;
}

:deep(.nm-search-form-item-selected .el-select__wrapper),
:deep(.nm-search-form-item-selected .el-input__wrapper) {
  @apply bg-[--el-color-primary-light-9] !shadow-none dark:bg-dark-900;
}

:deep(.nm-search-form-item-selected .el-select__placeholder),
:deep(.nm-search-form-item-selected .el-input__inner),
:deep(.nm-search-form-item-selected .el-input__prefix-inner) {
  @apply text-[--el-color-primary];
}

:deep(.nm-search-form-item-default .el-select__wrapper),
:deep(.nm-search-form-item-default .el-input__wrapper) {
  @apply bg-[#f8f8f9] !shadow-none dark:bg-dark-900;
}
</style>
