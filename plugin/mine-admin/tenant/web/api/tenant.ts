/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { PageList, ResponseStruct } from '#/global'

export interface TenantVo {
  id?: number
  name?: string
  account_count?: number
  contact_name?: string
  contact_phone?: string
  bind_domain?: string
  expire_at?: string
  status?: 1 | 2
  remark?: string
  created_at?: string
  user?: Record<string, any>
  package?: Record<string, any>
  username?: string
  password?: string
}

export interface TenantSearchVo {
  name?: string
  user?: string
  contact_name?: string
  expire_at?: string
  status?: number
}

export function page(data: TenantSearchVo): Promise<ResponseStruct<PageList<TenantVo>>> {
  return useHttp().get('/admin/plugin/tenant/list', { params: data })
}

export function create(data: TenantVo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/plugin/tenant', data)
}

export function save(id: number, data: TenantVo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/plugin/tenant/${id}`, data)
}

export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/plugin/tenant', { data: ids })
}
