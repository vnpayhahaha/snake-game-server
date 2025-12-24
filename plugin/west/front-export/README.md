<div align="center">
  <h1>FrontExport纯前端导出插件</h1>
</div>

<div align="center">

基于 [MineAdmin 3.0](https://www.mineadmin.com/) 的插件。

</div>

# 特性

简单高效的前端插件
支持Excel、CSV格式
动态配置导出字段

# 下载安装
- 后台应用市场下载插件
- 命令安装，在后端根目录下执行命令：
```sh
php bin/hyperf.php mine-extension:download west/front-export
```
```sh
php bin/hyperf.php mine-extension:install west/front-export --yes
```
- 安装前端依赖包
```sh
pnpm add exceljs
pnpm add file-saver
```

- 卸载插件
```sh
php bin/hyperf.php mine-extension:uninstall west/front-export --yes
```

# 使用方法
### 前端
- 插件将自动注入在MaProTable工具栏中

# 界面预览
![](https://pan.imgbed.link/file/248033)
![](https://pan.imgbed.link/file/248034)
![](https://pan.imgbed.link/file/248035)


# 相关链接
- [MineAdmin 指南](https://doc.mineadmin.com/zh/guide/introduce/mineadmin.html)
- [MineAdmin 前端](https://doc.mineadmin.com/zh/front/base/concept.html)
- [MineAdmin 后端](https://doc.mineadmin.com/zh/backend/)
- [MineAdmin 常见问题](https://doc.mineadmin.com/zh/faq/)
