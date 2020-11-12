系统扩展
=======

本文描述平台的扩展和修改方法。

## Types of extensions(扩展类型)

平台的核心行为可以通过以下三种基础方式来扩展或修改:
* Themes(主题): Theme用于修改系统界面。Theme包含以下文件：
    * Template file(模板文件): 修改HTML标记和系统输出的其他内容。
    * CSS文件: 修改应用于HTML的样式。
    * Javascript,Flash,图片和其他文件。
    
参考[主题系统和输出API](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21theme.api.php/group/theme_render/8.7.x)和[主题开发](https://www.drupal.org/docs/8/theming)文档获取更多信息。

* Modules(模块):
模块可以使用以下一种或多种方式来添加或修改Drupal提供的行为和功能。可以查看[模块开发](https://www.drupal.org/developing/modules/8)获取更多信息.
* Installation profiles(发行包):
安装文件被用来创建发布(行)包，此包可以打包成一套站点运行包，也可理解为产品包。运行此包，可以完整定义一个产品所需的所有功能，自动安装完成后即可在线运行。 发行包的相关信息可以查看[发行包开发](https://www.drupal.org/developing/distributions)获取更多信息。

### Alteration methods for modules(模块的扩展方法)
Drupal提供了以下一些方式来扩展核心模块或其他模块的功能。
* Hooks(钩子):
  模块定义的特定的函数名称，这些钩子可在特定的时间执行特定的功能；通常用来修改特定的数据或行为。可以查看[钩子](hook.md)获取更多的信息.
* Plugins(插件):
  模块定义的插件类，这些类在特定的时间被发现和实例化再添加相应的功能。可以查看[插件](plugins.md)获取更多信息.
* Entities(实体):
  Drupal模块中提供的特殊插件类型，这些类型属于实体类型，包括内容实体和配置实体。可以查看[实体](entity_api.md)获取更多信息。
* Services(服务):
  Drupal中执行基础操作的类，比如访问数据或发送eMail，可以查看[服务与依赖注入容器](container.md)以获取更多信息。
* Routing(路由): 支持提供和修改Drupal响应的路由，或者使用事件监听类修改路由行为。可以查看[路由和菜单API](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Menu%21menu.api.php/group/menu/8.7.x)以获取更多信息。
* Events(事件): 模块可以注册为事件订阅者, 当事件被分发，每个已注册的事件订阅者都会被调用。可以查看[事件](https://api.drupal.org/api/drupal/core%21core.api.php/group/events/8.7.x)以获取更多信息。

### *.info.yml 文件
*.info.yml必须单独放在每一个文件夹中，用来定义该文件夹是一个独立的模块文件。查看[\Drupal\Core\Extension\InfoParserInterface::parse()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Extension%21InfoParserInterface.php/function/InfoParserInterface%3A%3Aparse/8.7.x)获取更多关于模块定义的信息。

### File(文件)
`core/core.api.php`, line 1275
核心API文档。
