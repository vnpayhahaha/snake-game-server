/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Plugin } from '#/global'
import type { App } from 'vue'
import type { MaProTableToolbar } from '@mineadmin/pro-table'
import { useProTableToolbar } from '@mineadmin/pro-table'
import NmFrontExport from '$/west/front-export/views/index.vue'

const pluginConfig: Plugin.PluginConfig = {
  // eslint-disable-next-line unused-imports/no-unused-vars
  install(app: App) { },
  hooks: {
    start: (config): any => {
      console.log('您正在使用插件', `插件名称: ${config.info.description}`, `插件版本: ${config.info.version}`)
    },
    setup: (): any => {
      // 注册ICON
      const tableToolBar = useProTableToolbar()
      const newTool: MaProTableToolbar = {
        name: 'i-ci:share-ios-export',
        order: 6,
        show: true,
        render: () => NmFrontExport,
      }
      tableToolBar.add(newTool)
    },
  },
  config: {
    enable: true,
    info: {
      name: 'west/front-export',
      version: '1.0.1',
      author: 'west',
      description: '纯前端数据导出插件',
    },
  },
}

export default pluginConfig
