<div align="center">
  <h1>系统设置插件</h1>
</div>

<div align="center">

基于 [MineAdmin 3.0](https://www.mineadmin.com/) 的插件。

</div>

# 特性

好用、好看、轻量化

## 全面

MineAdmin 3.0 系统设置插件，提供设置持久化保存的功能，方便前端和其他业务调用静态数据

# 下载安装
- 后台应用市场下载插件
- 命令安装，在后端根目录下执行命令：
```sh
php bin/hyperf.php mine-extension:download west/sys-settings
```
```sh
php bin/hyperf.php mine-extension:install west/sys-settings --yes
```

# 使用方法
## 后端
后端提供了一个助手类，可以快捷调用系统配置数据
```php
// 获取分组信息，如果不传入参数，则获取所有分组信息
\Plugin\West\SysSettings\Helper\Helper::getSysSettingType('testType');
// 获取某个配置所有信息
\Plugin\West\SysSettings\Helper\Helper::getSysSettingByTypeCode('testType');
```

# 界面预览
<img src=https://pan.imgbed.link/file/243717 />
<img src=https://pan.imgbed.link/file/243718 />
<img src=https://pan.imgbed.link/file/243719 />


# 相关链接
- [MineAdmin 指南](https://doc.mineadmin.com/zh/guide/introduce/mineadmin.html)
- [MineAdmin 前端](https://doc.mineadmin.com/zh/front/base/concept.html)
- [MineAdmin 后端](https://doc.mineadmin.com/zh/backend/)
- [MineAdmin 常见问题](https://doc.mineadmin.com/zh/faq/)
