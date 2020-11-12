平台开发文档指南
==========

* [平台开发方法](平台开发方法.md)
* 重要的背景概念
    * [功能扩展](extending.md)
    * [信息类型](info_types.md)
* 用户界面
    * [Forms(表单)](form_api.md)
* 存储和检索数据
    * [Entities(实体)](entity_api.md)
    * [Configuration(配置)](config_api.md)
    * [Views(视图)](views.md)
    * [Database abstraction layer(数据抽象层)](database.md)
* [Plugins(插件)](plugins.md)
* [Render API](render_api.md)

## 在Firefox里查看本文档的方法
1. 设置markdown为文本，避免Firefox下载md文件
编辑~/.local/share/mime/packages/text-markdown.xml文件，内容为：
```
<?xml version="1.0"?>
<mime-info xmlns='http://www.freedesktop.org/standards/shared-mime-info'>
  <mime-type type="text/plain">
    <glob pattern="*.md"/>
    <glob pattern="*.mkd"/>
    <glob pattern="*.markdown"/>
  </mime-type>
</mime-info>
```
2. 为Firefox安装[Markdown插件](https://addons.mozilla.org/en-US/firefox/addon/gitlab-markdown-viewer/)。

## Markdown文档编辑
* [Markdown语法基础](markdown.md)
