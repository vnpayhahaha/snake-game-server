/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import excel from '../assets/edit.svg'
import word from '../assets/word.svg'

interface ItemOption {
  label: string // 直接作为字符串类型
  value: string | number
  describe: string
  icon: string
}

export const reportType = reactive<ItemOption[]>([
  {
    label: 'frontExport.standardReport', // 直接返回翻译的字符串
    value: 'STANDARD',
    describe: 'frontExport.csvStandardReport', // 直接返回翻译的字符串
    icon: excel,
  },
  {
    label: 'frontExport.customReport', // 直接返回翻译的字符串
    value: 'CUSTOMIZE',
    describe: 'frontExport.customReportDescription', // 直接返回翻译的字符串
    icon: word,
  },
])
