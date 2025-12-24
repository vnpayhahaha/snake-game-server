<script setup lang="tsx">
import { uploadLocal } from '$/west/tinymce/utils/upload.ts'
import { useColorMode } from '@vueuse/core'
// 核心包
import Editor from '@tinymce/tinymce-vue'
import tinymce from 'tinymce/tinymce' // ✅ 解决 ReferenceError
import 'tinymce/themes/silver' // 主题
import 'tinymce/icons/default' // 图标
import 'tinymce/models/dom' // DOM模型

import 'tinymce/icons/default/icons'

import pluginList from '../utils/pluginList.ts'
import '../utils/langs/zh_CN.js'

defineOptions({ name: 'NmTinyMCE' })

const props = withDefaults(
  defineProps<{
    height?: number
    language?: string
    toolbar?: Array<string>
  }>(),
  {
    height: 500, // 组件高度
    language: 'zh_CN', // 语言标识
    toolbar: () => [
      'undo redo | fontfamily fontsize | bold italic | RSelector |',
      'styleselect | fontselect | formats | align | numlist bullist | link image | save print preview fullscreen code | charmap emoticons | pagebreak anchor codesample | ltr rtl',
    ], // 富文本编辑器工具
  },
)

const isDark = useColorMode()
const model = defineModel<string>()
const editorRef = ref<any>(null)
const isOpenResource = ref<boolean>(false)
// 初始化配置
const editorInit = reactive({
  selector: 'textarea',
  height: props?.height, // 默认高度
  min_height: props?.height, // 最小高度
  statusbar: false,
  language: props?.language, // 语言标识
  branding: false, // 不显示右下角logo
  auto_update: false, // 不进行自动更新
  promotion: false, // 禁用升级提示
  resize: true, // 可以调整大小
  menubar: 'file edit view insert format tools table help', // 菜单栏
  skin_url: isDark.value === 'dark'
    ? '/tinymce/skins/ui/oxide-dark'
    : '/tinymce/skins/ui/oxide', // 动态路径
  content_css: isDark.value === 'dark'
    ? '/tinymce/skins/content/dark/content.css'
    : '/tinymce/skins/content/default/content.css', // 动态路径
  toolbar_mode: 'wrap',
  plugins: pluginList, // 插件
  toolbar: props?.toolbar, // 功能按钮
  image_caption: false,
  paste_data_images: true,
  // 上传配置
  images_upload_url: '/dev/admin/attachment/upload',
  images_upload_handler: uploadLocal,
  // 自定义功能
  setup: (editor) => { // 自定义图标内容及触发点击事件等功能
    editor.ui.registry.addButton('RSelector', {
      text: '资源选择器',
      tooltip: '资源筛选器',
      onAction: () => {
        isOpenResource.value = true
      },
    })
  },
})

// 监听 isDark 的变化
watch(isDark, () => {
  // 更新皮肤和样式路径
  editorInit.skin_url = isDark.value === 'dark'
    ? '/tinymce/skins/ui/oxide-dark'
    : '/tinymce/skins/ui/oxide'

  editorInit.content_css = isDark.value === 'dark'
    ? '/tinymce/skins/content/dark/content.css'
    : '/tinymce/skins/content/default/content.css'
  // 先销毁已有实例
  if (tinymce?.activeEditor) {
    tinymce.activeEditor.destroy()
  }

  // 等 DOM 稳定后再初始化
  nextTick(() => {
    tinymce.init({
      ...editorInit,
    })
  })
})

// 暴露方法
defineExpose({
  editor: editorRef,
})
</script>

<template>
  <Editor ref="editorRef" v-model="model" :init="editorInit" />
  <MaResourcePicker
    v-model:visible="isOpenResource" @confirm="(selected) => {
      const fileList = selected.map(item => `<img src='${item.url}' alt='${item.ogirin_name}' />`);
      editorRef?.getEditor().insertContent(fileList.join(''))
    }"
  />
</template>

<style scoped lang="scss"></style>
