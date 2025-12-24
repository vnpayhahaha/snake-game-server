/*
 * @Date: 2024-10-23 13:41:16
 * @LastEditors: west_ng 457395070@qq.com
 * @LastEditTime: 2024-10-26 23:03:35
 * @FilePath: /MineAdmin/web/src/plugins/west/table-col/index.ts
 */
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

const pluginConfig: Plugin.PluginConfig = {
  // eslint-disable-next-line unused-imports/no-unused-vars
  install(app: App) {},
  hooks: {
    start: (config): any => {
      console.log('您正在使用插件', `插件名称: ${config.info.description}`, `插件版本: ${config.info.version}`)
    },
  },
  config: {
    enable: true,
    info: {
      name: 'west/tinymce',
      version: '1.0.3',
      author: 'west',
      description: 'TinyMCE是一款功能强大且广泛使用的开源富文本编辑器',
    },
  },
}

export default pluginConfig
