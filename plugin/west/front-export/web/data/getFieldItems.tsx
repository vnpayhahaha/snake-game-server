import { Search } from '@element-plus/icons-vue'
import type { MaFormItem } from '@mineadmin/form'

export default function getFieldItems(columns: any[], t: any, model: any): MaFormItem[] {
  const checkAll = ref(false)
  const isIndeterminate = ref(false)
  const searchKeyword = ref('') // 用于保存搜索关键词

  // 过滤字段
  const filteredFields = computed(() => {
    if (!searchKeyword.value) {
      return columns // 返回所有字段
    }
    return columns.filter((field: any) =>
      field.label.toLowerCase().includes(searchKeyword.value.toLowerCase())
      || field.prop.toLowerCase().includes(searchKeyword.value.toLowerCase()),
    )
  })

  // 更新选中状态
  const updateCheckStatus = () => {
    isIndeterminate.value = model.fields.length > 0 && model.fields.length < filteredFields.value.length
    checkAll.value = model.fields.length === filteredFields.value.length
  }

  // 处理全选/取消全选
  const handleCheckAll = (val: boolean) => {
    if (val) {
      // 全选时，选中所有字段
      model.fields = [...filteredFields.value] // 将所有字段对象加入 model.fields
    }
    else {
      // 取消全选时，清空 model.fields
      model.fields = [] // 清空选中的字段
    }
    isIndeterminate.value = false
  }

  return [
    // 搜索框部分
    {
      prop: 'keyword',
      render: 'input',
      renderProps: {
        suffixIcon: Search,
        placeholder: t('frontExport.searchFieldName', { msg: t('frontExport.fieldNameRequired') }),
        onChange: (value: string) => {
          // 处理本地搜索过滤
          searchKeyword.value = value
        },
      },
      itemProps: {
        rules: [{ required: true, message: t('frontExport.searchFieldName', { msg: t('frontExport.fieldNameRequired') }) }],
      },
    },
    // 选择字段部分
    {
      prop: 'fields',
      render: () => (
        <div class="flex flex-1 flex-col">
          <div class="flex gap-2.5">
            <el-checkbox
              v-model={checkAll.value}
              indeterminate={isIndeterminate.value}
              onChange={() => handleCheckAll(checkAll.value)}
            >
              {t('frontExport.selectAll')}
            </el-checkbox>
            <el-text>
              {t('frontExport.selected')}
              (
              {model?.fields?.length ?? 0}
              )
            </el-text>
          </div>
          <div class="flex-1">
            <el-checkbox-group
              v-model={model.fields}
              onChange={updateCheckStatus}
              class="grid grid-cols-1 gap-4 md:grid-cols-3 sm:grid-cols-2"
            >
              {
                filteredFields.value.length > 0
                  ? filteredFields.value.map((item: any, index: number) => (
                      <el-checkbox key={index} label={item} value={item}>
                        {item.label}
                      </el-checkbox>
                    ))
                  : null
              }
            </el-checkbox-group>

            {
              filteredFields.value.length === 0 && (
                <div class="w-full flex justify-center">
                  <el-empty description={t('frontExport.noMatchingFields')} />
                </div>
              )
            }
          </div>
        </div>
      ),
    },
  ]
}
