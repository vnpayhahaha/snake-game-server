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

export interface TenantPackageVo {
  id?: number
  package_name?: string
  status?: 1 | 2
  remark?: string
  created_at?: string
  menus?: any[]
  tenant?: Record<string, any>
}

export interface TenantPackageSearchVo {
  package_name?: string
  status?: number
}

export function page(data: TenantPackageSearchVo): Promise<ResponseStruct<PageList<TenantPackageVo>>> {
  return useHttp().get('/admin/plugin/tenant_package/list', { params: data })
}

export function create(data: TenantPackageVo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/plugin/tenant_package', data)
}

export function save(id: number, data: TenantPackageVo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/plugin/tenant_package/${id}`, data)
}

export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/plugin/tenant_package', { data: ids })
}
