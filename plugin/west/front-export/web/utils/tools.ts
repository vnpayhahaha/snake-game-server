/**
 * 提取每一列的 `prop` 和 `label` 字段，返回一个对象数组。
 * 如果 `label` 是一个函数，执行该函数并保留 `prop` 和计算后的 `label`。
 * 排除掉 `type` 为 'index', 'selection', 'operation' 的列。
 *
 * @param {Array} columns - 包含列配置的数组，每个元素可能包含 `prop` 和 `label` 字段。
 * @returns {Array} - 包含 `prop` 和 `label` 的对象数组
 */
export function extractLabels(columns: any[]): any[] {
  return columns
    .map((col) => {
      // 排除掉 `type` 为 'index', 'selection', 'operation' 的列
      if (['index', 'selection', 'operation'].includes(col.type)) {
        return null // 返回 null，表示需要排除这一列
      }

      // 判断 `label` 是否为函数
      const label = typeof col.label === 'function' ? col.label() : col.label

      return label !== null && label !== undefined
        ? { ...col, label }
        : null // 保留 `prop` 和 `label`
    })
    .filter(item => item !== null) // 过滤掉 `null` 的值，保留有效的列
}

/**
 * 根据字段定义提取指定字段数据
 * @param data 原始数据数组
 * @param fields 包含字段名和显示名的数组
 * @returns 只包含指定字段的新数组
 */
export function getExportData(data: any[], fields: any[]): any[] {
  return data.map((row) => {
    const result: any = {}

    fields.forEach((field) => {
      const { prop, label, cellRenderTo, cellRender } = field
      let value = getNestedValue(row, prop)

      // 如果存在 cellRender，则使用该渲染方式
      if (typeof cellRender === 'function') {
        const renderedVNode = cellRender({ row })
        value = extractTextFromVNode(renderedVNode) ?? value
      }

      value = resolveDictValue(value, cellRenderTo)

      // 防止科学计数
      if (typeof value === 'number' && value.toString().length >= 12) {
        value = `'${value}'`
      }

      result[label] = value
    })

    return result
  })
}

/**
 * 支持解析嵌套对象路径，如 "shop.advertiser_name"
 */
function getNestedValue(obj: any, path: string): any {
  try {
    return path.split('.').reduce((acc, key) => {
      return acc && acc[key] !== undefined ? acc[key] : undefined
    }, obj)
  }
  catch (error) {
    console.error(`Error accessing property ${path}:`, error)
    return undefined
  }
}

/**
 * 将原始值根据字典转换为显示文本
 * @param value 原始值（字符串、数字、数组）
 * @param cellRenderTo 字段中定义的 cellRenderTo 配置
 */
export function resolveDictValue(value: any, cellRenderTo: any): string {
  if (!cellRenderTo?.props) { return value }

  const props = cellRenderTo.props
  const dictName = props.dictName || props.dict
  const options = props.options

  // 通过字典存储获取字典数据
  const dictStore = useDictStore()
  const dictionaryData = dictStore.find(dictName) || null

  if (!dictionaryData) {
    return value // 如果没有字典数据，直接返回原始值
  }

  // 处理数组类型
  if (Array.isArray(value)) {
    const resolvedValues = value.map(val => resolveSingleValue(val, dictionaryData, options))
    return resolvedValues.join(', ')
  }

  // 处理单个值
  return resolveSingleValue(value, dictionaryData, options)
}

function resolveSingleValue(val: any, dictionaryData?: any, options?: any[]): string {
  if (options) {
    const match = options.find(opt => opt.value === val)
    return match ? match.label : val
  }

  if (Array.isArray(dictionaryData)) {
    const match = dictionaryData.find((item: any) => String(item.value) === String(val))
    return match ? match.label : val
  }

  if (dictionaryData && typeof dictionaryData === 'object') {
    return dictionaryData[val] ?? val
  }

  return val
}
// 处理奇奇怪怪的东西
function extractTextFromVNode(vnode: any, depth = 0): string | undefined {
  const indent = '  '.repeat(depth)

  if (!vnode) {
    return undefined
  }

  if (typeof vnode === 'string') {
    return vnode
  }

  if (vnode.type === Text) {
    return vnode.children
  }

  if (vnode.children) {
    if (typeof vnode.children === 'string') {
      return vnode.children
    }

    if (Array.isArray(vnode.children)) {
      return vnode.children
        .map((child, index) => {
          return extractTextFromVNode(child, depth + 2)
        })
        .filter(Boolean)
        .join('')
    }

    if (typeof vnode.children === 'object') {
      // ⬇️ 如果是 slot 函数形式（children.default 是函数）
      if (typeof vnode.children.default === 'function') {
        const slotContent = vnode.children.default()

        if (Array.isArray(slotContent)) {
          return slotContent
            .map((child, index) => {
              return extractTextFromVNode(child, depth + 2)
            })
            .filter(Boolean)
            .join('')
        }
        else {
          return extractTextFromVNode(slotContent, depth + 1)
        }
      }

      // fallback：递归 object 类型 children
      return extractTextFromVNode(vnode.children, depth + 1)
    }
  }
  return undefined
}

/**
 * 从函数字符串中匹配 URL
 * @returns 匹配到的 URL 或 null
 * @param func
 */
export function extractUrlFromFunc(func: (...args: any[]) => void | string): string | null {
  let funcString: string

  // 判断是否是函数并转换为字符串
  if (typeof func === 'function') {
    funcString = func.toString()
  }
  else {
    funcString = func
  }

  // 使用正则表达式匹配 URL
  const regex = /get\(\s*["'`](.*?)["'`]\s*,/
  const matchResult = funcString.match(regex)

  return matchResult ? matchResult[1] : null
}
