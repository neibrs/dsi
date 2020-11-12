Module(模块)
=============

模块可安装、卸载、扩展、升级的组件。

技巧：浏览器通过`admin/modules`这个地址可以查看系统可用的模块。

每个模块是一个有`模块名.info.yml`文件的目录。
```yaml
name: Node          # 模块名称
type: module        # 模块的类型必须是module
description: 'Allows content to be submitted to the site and displayed on pages.'   # 模块描述
package: Core       # 所块所属的包名
version: VERSION    # 模块的版本
core: 8.x           # 核心的版本
configure: entity.node_type.collection
dependencies:       # 本模块所依赖的其他模块
  - drupal:text
```

核心提供的模块在`core/modules`目录，我们开发的模块在`modules/dsi`目录。

模块目录包含的内容包括：
* `config` 配置文件目录
* `src` 源代码目录
* `templates` 模板模块
* `tests` 自动化测试脚本目录
* `模块名.info.yml` 模块定义文件
* `模块名.install` 模块安装代码
* `模块名.links.actoin.yml` action 按钮链接配置
* `模块名.links.menu.yml` 菜单配置
* `模块名.links.task.yml` 页签配置
* `模块名.module`  [钩子](hook.md)代码
* `模块名.permissions.yml` 权限定义文件
* `模块名.rouging.yml` [路由](routing.md)定义文件
* `模块名.services.yml` [服务](service.md)定义文件

## 启用模块
通常通过容器内的命令行来启用模块;
```bash
vendor/bin/drupal moi 模块名
```

## 如何来定义模块？
通常通过容器内的命令行生成模块基础代码;
```bash
vendor/bin/drupal gm
```
