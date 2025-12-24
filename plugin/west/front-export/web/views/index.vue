<script setup lang="ts">
import Form from '../components/form.vue'
import Field from '../components/field.vue'
import type { UseDrawerExpose } from '@/hooks/useDrawer.ts'
import useDrawer from '@/hooks/useDrawer.ts'
import type { MaProTableExpose } from '@mineadmin/pro-table'
import { extractLabels, extractUrlFromFunc, getExportData } from '$/west/front-export/utils/tools.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'
import useDialog from '@/hooks/useDialog.ts'
import { useMessage } from '@/hooks/useMessage.ts'
import { page } from '$/west/front-export/api/getData.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import { exportCSV, exportExcel } from '$/west/front-export/utils/exportExcel.ts'
import { hideExportLoading, showExportLoading } from '$/west/front-export/utils/exportLoading.ts'

defineOptions({ name: 'NmFrontExport' })
const { proxy } = defineProps<{ proxy: MaProTableExpose }>()
const t = useTrans().globalTrans
const fieldRef = ref()
const formRef = ref()
const options = reactive<any>(proxy.getProTableOptions()) // 初始化为空对象而不是 null
const columns = reactive<any>(proxy.getTableColumns())
const exportData = reactive<any>([])
const url = extractUrlFromFunc(options?.requestOptions?.api) ?? null
const msg = useMessage()
const data = reactive({
  fields: extractLabels(proxy.getTableColumns() ?? []),
})
// 标识数据是否加载完成
const isDataLoaded = ref(false)

// 获取数据的方法
async function getList() {
  if (url === null) {
    msg.error(t('frontExport.apiNotConfigured'))
    return Promise.reject(new Error(t('frontExport.apiMissing')))
  }

  try {
    // 分页加载数据
    let pageIndex = 0
    const pageSize = 100
    const allData: any[] = []

    // 这里假设你可以循环请求分页数据直到所有数据都加载完
    while (true) {
      const res = await page(url, { page: pageIndex, page_size: pageSize })

      if (res.code === ResultCode.SUCCESS) {
        allData.push(...res.data.list) // 将每一页数据追加到 allData 中
        if (res.data.list.length < pageSize) {
          break // 如果返回的数量少于每页数量，说明所有数据已经加载完
        }
        pageIndex++ // 下一页
      }
      else {
        return Promise.reject(new Error(t('frontExport.fetchFailed')))
      }
    }

    exportData.push(...allData) // 将所有数据存储到 exportData
    isDataLoaded.value = true // 标记数据已加载完成
  }
  catch (error) {
    msg.error(t('frontExport.requestFailed'))
    isDataLoaded.value = false // 标记数据加载失败
    return Promise.reject(error)
  }
  finally {
    hideExportLoading()
  }
}

const maDrawer: UseDrawerExpose = useDrawer({
  size: '60%', // 宽度
  ok: (okLoadingState: (state: boolean) => void) => {
    const elForm = formRef.value.maForm.getElFormRef()
    elForm.validate().then(() => {
      formRef.value.add().then(async (res: any) => {
        try {
          showExportLoading(t('frontExport.exportingData')) // 显示 loading
          exportData.length = 0 // 清空旧数据
          await getList() // 异步获取数据（你已经加了 finally 调用 hide）

          const headers = res.fields.map(field => field.label)
          const result = getExportData(exportData, res.fields)

          if (res.format === 'xlsx') {
            exportExcel(result, headers, res.name)
          }
          else {
            exportCSV(result, headers, res.name)
          }
        }
        catch (error) {
          console.error(t('frontExport.operationFailed'), error)
          msg.error(t('frontExport.retryOperationFailed'))
        }
      }).catch((err: any) => {
        // msg.alertError(err)
      })
    }).catch((err: any) => {
      // msg.error(err.message)
    })
  },
})

const maDialog: UseDialogExpose = useDialog({
  alignCenter: 'center',
  width: '800px',
  ok: (okLoadingState: (state: boolean) => void) => {
    okLoadingState(true)
    fieldRef.value.add().then((res: any) => {
      data.fields = res
      maDialog.close()
    }).catch((error: Error) => {
      msg.error(error.message)
    }).finally(() => {
      okLoadingState(false)
    })
  },
})

provide('options', options)
provide('columns', columns)
</script>

<template>
  <el-button class="el-button is-circle ml-[12px]">
    <ma-svg-icon
      name="i-ci:share-ios-export" size="1.2em" @click="() => {
        maDrawer.setTitle(t('frontExport.exportConfig'))
        maDrawer.open()
      }"
    />
  </el-button>
  <component :is="maDrawer.Drawer">
    <template #default>
      <Form ref="formRef" :data="data" :ma-dialog="maDialog" />
    </template>
  </component>
  <component :is="maDialog.Dialog">
    <template #default>
      <!-- 新增、编辑表单 -->
      <Field ref="fieldRef" />
    </template>
  </component>
</template>

<style scoped lang="scss"></style>
