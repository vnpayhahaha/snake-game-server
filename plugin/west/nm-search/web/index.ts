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
import nmSearch from '$/west/nm-search/views/index.vue'

const pluginConfig: Plugin.PluginConfig = {
  install(app) {
    // 注册全局路由
    app.component('NmSearch', nmSearch)
  },
  hooks: {
    start: (config): any => {
      console.log('您正在使用插件', `插件名称: ${config.info.description}`, `插件版本: ${config.info.version}`)
    },
    setup: (): any => {

    },
  },
  config: {
    enable: true,
    info: {
      name: 'west/nm-search',
      version: '1.0.1',
      author: 'west',
      description: '小小牛马搜索条',
    },
  },
}

export default pluginConfig
