Plugin(插件)
============

为了提供足够的灵活性和可扩展性，系统大量的功能都是通过插件实现的。

技巧：查看系统提供的所有插件
```
find -path '*/src/Plugin/*'
```

例如：
```php
namespace Drupal\node\Plugin\views\filter;              // 插件必须放到Plugin目录，filter插件必须放到views/filter目录

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filter by published status.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("node_status")                          // 在注释中说明插件类型
 */
class Status extends FilterPluginBase {                 // 基于正确的基类或接口
......
```

## 常用插件

* `Action` 实体处理，为批量操作等服务。
* `Block` 区块
* `Derivative` 根据现有定义提供其他插件定义
* `EntityReferenceSelection` 实体引用选择处理
* `FieldFormater` 字段格式化显示
* `FieldType` 字段类型
* `FieldWidget` 字段输入器件
* `LocalAcion` LocalAction菜单
* `WorkflowType` 工作流类型

其他常用插件包括：
[视图插件](views_plugin.md)

## 参考

[官方文档](https://api.drupal.org/api/drupal/core!core.api.php/group/plugin_api)
