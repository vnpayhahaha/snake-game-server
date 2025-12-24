import type { PageList, ResponseStruct } from '#/global'
import useHttp from '@/hooks/auto-imports/useHttp.ts'

/**
 * 动态请求 API 获取分页数据
 * @param url 请求的 API 路径
 * @param data 请求的参数
 * @returns 返回 API 请求的 Promise
 */
export function page(url: string, data: Record<string, any>): Promise<ResponseStruct<PageList<any>>> {
  return useHttp().get(url, { params: data })
}
