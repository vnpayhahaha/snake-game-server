<div align="center">
  <h1>巨量风格表格搜索组件</h1>
</div>

<div align="center">

基于 [MineAdmin 3.0](https://www.mineadmin.com/) 的插件。

</div>

# 特性


# 下载安装
- 后台应用市场下载插件
- 命令安装，在后端根目录下执行命令：
```sh
php bin/hyperf.php mine-extension:download west/nm-search
```
```sh
php bin/hyperf.php mine-extension:install west/nm-search --yes
```

- 卸载插件
```sh
php bin/hyperf.php mine-extension:uninstall west/nm-search --yes
```

# 使用方法
### 前端
- 通过MaProTable的插槽来实现，row表示显示多少个在工具条中，其余的则将在折叠面板中
```vue
<MaProTable ref="proTableRef" :options="options" :schema="schema">
  <template #toolbarLeft>
    <NmSearch :proxy="proTableRef" row="2" />
  </template>
</MaProTable>
```
# 界面预览



# 相关链接
- [MineAdmin 指南](https://doc.mineadmin.com/zh/guide/introduce/mineadmin.html)
- [MineAdmin 前端](https://doc.mineadmin.com/zh/front/base/concept.html)
- [MineAdmin 后端](https://doc.mineadmin.com/zh/backend/)
- [MineAdmin 常见问题](https://doc.mineadmin.com/zh/faq/)
