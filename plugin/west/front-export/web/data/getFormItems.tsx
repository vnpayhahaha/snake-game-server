/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MaFormItem } from '@mineadmin/form'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'
import MaDictRadio from '@/components/ma-dict-picker/ma-dict-radio.vue'
import WestCardRadio from '../components/WestCardRadio.vue'
import { reportType } from '$/west/front-export/data/type.ts'
import dayjs from 'dayjs'

export default function getFormItems(t: any, model: any, { maDialog }: { maDialog: UseDialogExpose }, options: any): MaFormItem[] {
  const isType = ref(false)
  model.name = `${(options?.header && typeof options.header.mainTitle === 'function' ? options.header.mainTitle() : t('frontExport.exportName'))}_${dayjs().format('YYYY_MM_DD')}_${dayjs().unix()}`
  model.name = model.name.replace(/[<>:"/\\|?*\s]+/g, '_')
  model.type = model.type || 'STANDARD'
  model.format = model.format || 'xlsx'
  return [
    {
      label: () => t('frontExport.fileName'),
      prop: 'name',
      render: 'input',
      renderProps: {
        placeholder: t('frontExport.enterFileName', { msg: t('frontExport.fileNameRequired') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('frontExport.enterFileName', { msg: t('frontExport.fileNameRequired') }) }],
      },
    },
    {
      label: () => t('frontExport.reportType'),
      prop: 'type',
      render: () => WestCardRadio,
      renderProps: {
        data: reportType,
        onChange: () => {
          isType.value = !isType.value
        },
      },
    },
    {
      label: () => t('frontExport.fileFormat'),
      prop: 'format',
      render: () => MaDictRadio,
      renderProps: {
        border: true,
        data: [
          {
            label: 'Excel',
            value: 'xlsx',
          }, {
            label: 'CSV',
            value: 'csv',
          },
        ],
      },
    },
    {
      label: () => `${t('frontExport.reportFields')} (${t('frontExport.totalFields', { count: model.fields?.length ?? 0 })})`,
      prop: 'fields',
      render: () => {
        return (
          <div class="grid grid-cols-1 w-full gap-[10px] lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2">
            {model.fields?.map((item: any, index: number) => (
              <div class="flex items-center gap-2.5" key={index}>{item.label}</div>
            ))}
          </div>
        )
      },
      itemSlots: {
        label: () => {
          return (
            <div class="flex flex-1 items-center justify-between">
              <div class="">
                {t('frontExport.reportFields')}
                （
                {t('frontExport.totalFields', { count: model.fields?.length ?? 0 })}
                ）
              </div>
              <el-button
                v-show={isType.value}
                type="primary"
                plain
                onClick={() => {
                  maDialog.setTitle(t('frontExport.configureFields'))
                  maDialog.open()
                }}
              >
                {t('frontExport.configureFields')}
              </el-button>
            </div>
          )
        },
      },
    },
  ]
}
