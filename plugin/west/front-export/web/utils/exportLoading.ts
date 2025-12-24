// src/utils/loading/exportLoading.ts

import { ElLoading } from 'element-plus'

let loadingInstance: ReturnType<typeof ElLoading.service> | null = null

/**
 * 显示导出 Loading
 * @param message 提示文字（可选，默认：'正在导出数据，请稍候...'）
 */
export function showExportLoading(message: string = '正在导出数据，请稍候...') {
  if (loadingInstance) { return } // 避免重复调用

  loadingInstance = ElLoading.service({
    lock: true,
    text: message,
    background: 'rgba(255, 255, 255, 0.8)',
  })
}

export function hideExportLoading() {
  loadingInstance?.close()
  loadingInstance = null
}
