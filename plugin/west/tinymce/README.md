<div align="center">
  <h1>TinyMCE富文本编辑器</h1>
</div>

<div align="center">

基于 [MineAdmin 3.0](https://www.mineadmin.com/) 的插件。

</div>

# 特性

最值得信赖且功能丰富的所见即所得富文本编辑器
The Most Trusted and Feature-rich WYSIWYG Rich Text Editor

# 下载安装
- 后台应用市场下载插件
- 命令安装，在后端根目录下执行命令：
```sh
php bin/hyperf.php mine-extension:download west/tinymce
```
```sh
php bin/hyperf.php mine-extension:install west/tinymce --yes
```
- 安装前端依赖包
```sh
pnpm add tinymce "@tinymce/tinymce-vue"
```

- 关于打包不显示问题
```sh
安装插件完成后确认 `public/tinymce` 目录资源是否存在
```



- 卸载插件
```sh
php bin/hyperf.php mine-extension:uninstall west/tinymce --yes
```

# 使用方法
### 前端
- 组件没有进行全局注册，所以在时候的时候需要导入组件
```typescript
import NmTinyMCE from '$/west/tinymce/views/index.vue'

<NmTinyMCE v-model="testData" height="200" />
```
# 配置说明
- 
```typescript
const editorInitConfig = defineProps({
    height: {
        type: Number,
        default: 500,
    },
    selector: {
        type: String,
        default: 'textarea',
    },
    language: {
        type: String,
        default: 'zh_CN',
    },
    toolbar: {
        type: Array,
        default: () => [
            'undo redo | fontfamily fontsize | bold italic |',
            'styleselect | fontselect | formats | align | numlist bullist | link image | save print preview fullscreen code | charmap emoticons | pagebreak anchor codesample | ltr rtl',
        ],
    },
})
```


# 相关链接
- [MineAdmin 指南](https://doc.mineadmin.com/zh/guide/introduce/mineadmin.html)
- [MineAdmin 前端](https://doc.mineadmin.com/zh/front/base/concept.html)
- [MineAdmin 后端](https://doc.mineadmin.com/zh/backend/)
- [MineAdmin 常见问题](https://doc.mineadmin.com/zh/faq/)
