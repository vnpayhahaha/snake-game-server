/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { App } from 'vue'
import type { Plugin } from '#/global'
import useCache from '@/hooks/useCache.ts'

const replaceUrl = [
  '/admin/passport/login',
  '/admin/passport/getInfo',
  '/admin/permission/menus',
  '/admin/menu/list',
]

const newUrl = [
  '/admin/plugin/tenant/login',
  '/admin/plugin/tenant/getInfo',
  '/admin/plugin/tenant/menus',
  '/admin/plugin/tenant/menus',
]

const pluginConfig: Plugin.PluginConfig = {
  install(app: App) {},
  config: {
    enable: true,
    info: {
      name: 'mine-admin/tenant',
      version: '1.0.0',
      author: 'X.Mo',
      description: '多租户功能',
      order: 99999,
    },
  },
  hooks: {
    networkRequest: (request: any) => {
      request.headers!.tenant_id = useCache().get('tenant')?.id ?? null
      const index = replaceUrl?.findIndex((u: string) => u === request.url)
      if (index > -1) {
        request.url = newUrl[index]
      }
    },
    networkResponse: (response: any) => {
      const index = newUrl?.findIndex((u: string) => u === response?.config?.url)
      if (index === 1 && response.data?.code === 200) {
        const { data } = response?.data
        useCache().set('tenant', data?.tenant ?? {})
      }
    },
    logout: () => {
      useCache().set('tenant', null)
    },
  },
}

export default pluginConfig
