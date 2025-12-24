import ExcelJS from 'exceljs'
import { saveAs } from 'file-saver'

/**
 * 公共函数：填充表格数据
 * @param worksheet 工作表
 * @param data 数据数组
 * @param headers 表头数组
 */
function fillData(worksheet: ExcelJS.Worksheet, data: any[], headers: string[]) {
  // 填充数据
  data.forEach((item) => {
    const row = headers.map(header => item[header] ?? '')
    worksheet.addRow(row)
  })
}

/**
 * 公共函数：计算列宽
 * @param data 数据数组
 * @param headers 表头数组
 * @param worksheet 工作表
 */
function setColumnWidth(worksheet: ExcelJS.Worksheet, data: any[], headers: string[]) {
  // 辅助函数：计算宽度（中文算2，英文算1）
  const getTextWidth = (text: any): number => {
    if (text === null || text === undefined) { return 0 }
    const str = text.toString()
    let width = 0
    for (const char of str) {
      width += /[\u4E00-\u9FA5]/.test(char) ? 2 : 1
    }
    return width
  }

  // 设置列宽
  headers.forEach((header, index) => {
    const lengths = data.map(item => getTextWidth(item[header]))
    const maxContentWidth = Math.max(...lengths, getTextWidth(header))
    worksheet.getColumn(index + 1).width = maxContentWidth + 2 // 加点边距
  })
}

/**
 * 导出 Excel 文件（.xlsx 格式）
 * @param data 数据数组
 * @param headers 表头数组
 * @param filename 文件名
 */
export function exportExcel(data: any[], headers: string[], filename = '导出表格.xlsx') {
  if (data.length === 0) {
    console.error('没有数据可以导出')
    return
  }

  const workbook = new ExcelJS.Workbook()
  const worksheet = workbook.addWorksheet('Sheet 1')

  // 添加表头
  worksheet.addRow(headers)

  // 表头样式
  const headerRow = worksheet.getRow(1)
  headerRow.font = { bold: true }
  headerRow.alignment = { horizontal: 'center', vertical: 'middle' }

  // 填充数据和设置列宽
  fillData(worksheet, data, headers)
  setColumnWidth(worksheet, data, headers)

  // 导出文件
  workbook.xlsx.writeBuffer().then((buffer) => {
    const blob = new Blob([buffer], {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    })
    saveAs(blob, filename)
  }).catch((err) => {
    console.error('导出失败', err)
  })
}

/**
 * 导出 CSV 文件（.csv 格式）
 * @param data 数据数组
 * @param headers 表头数组
 * @param filename 文件名
 */
export function exportCSV(data: any[], headers: string[], filename = '导出表格.csv') {
  if (data.length === 0) {
    console.error('没有数据可以导出')
    return
  }

  const csvRows: string[] = []

  // 添加表头
  csvRows.push(headers.join(','))

  // 填充数据
  data.forEach((item) => {
    const row = headers.map((header) => {
      const value = item[header] ?? ''
      // 转义 CSV 中的特殊字符
      return `"${value.toString().replace(/"/g, '""')}"`
    })
    csvRows.push(row.join(','))
  })

  // 生成 CSV 内容
  const csvContent = csvRows.join('\n')

  // 导出 CSV 文件
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  saveAs(blob, filename)
}
