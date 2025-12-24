/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn> and NEKGod<1559096467@qq.com>
 * @Link   https://github.com/mineadmin
 */
import type { ResponseStruct } from '#/global'
/**
 * SystemSettingConfigGroup API JS
 */

export interface ConfigGroupVo {
  id?: number// 主键ID
  name: string// 配置组名称
  code: string// 配置组标识
  icon?: string// 配置组图标 (可选)
  remark?: string
}

export interface ConfigGroupListVo {
  id?: number// 主键ID
  name: string// 配置组名称
  code: string// 配置组标识
  icon?: string// 配置组图标 (可选)
  remark?: string
  info: Array<ConfigVo>
  updated_at?: string
  created_at?: string
}

export interface ConfigVo {
  group_id?: number// 组ID
  key?: string// 配置键名
  value?: string// 配置值
  name?: string// 配置名称
  input_type?: string // 输入类型
  config_select_data?: Array<any> // 配置选项
  remark?: string
}

/**
 * 获取SystemSettingConfigGroup分页列表
 * @returns
 */
export function page(data: any): Promise<ResponseStruct<any>> {
  return useHttp().get('/system/ConfigGroup/list', { params: data })
}

/**
 * 添加SystemSettingConfigGroup
 * @returns
 */
export function create(data: any): Promise<ResponseStruct<any>> {
  return useHttp().post('/system/ConfigGroup', data)
}
/**
 * 更新SystemSettingConfigGroup数据
 * @returns
 */
export function save(id: number, data: any): Promise<ResponseStruct<any>> {
  return useHttp().put(`/system/ConfigGroup/${id}`, data)
}

/**
 * 将SystemSettingConfigGroup删除，有软删除则移动到回收站
 * @returns
 */
export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/system/ConfigGroup', { data: ids })
}
