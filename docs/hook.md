Hook(钩子)
==========

Hook(钩子)是在特定时间被发现和调用的特殊命名函数，通常用户更改行为和数据。

以下常用钩子必需全面掌握：

## `模块名.module`常用钩子:

* `hook_cron` 执行定期操作

* `hook_theme` 定义主题模板
* `hook_theme_suggestions_HOOK` 为特定的主题钩子提供替代命名建议
* `hook_preprocess_HOOK` 为特定主题钩子预处理主题变量

* `hook_entity_base_field_info` 为ContentEntity提供基础字段定义
* `hook_entity_extra_field_info` 为ContentEntity提供`伪字段`

* `hook_entity_insert` 响应新实体添加
* `hook_ENTITY_TYPE_insert` 响应新实体添加

* `hook_entity_update` 响应实体更新
* `hook_ENTITY_TYPE_update` 响应实体更新

* `hook_entity_delete` 响应实体删除
* `hook_ENTITY_TYPE_delete` 响应实体删除

* `hook_entity_load` 加载实体
* `hook_ENTITY_TYPE_load` 加载实体

* `hook_entity_view` 对实体的render数组进行修改
* `hook_ENTITY_TYPE_view` 对实体的render数组进行修改

* `hook_form_alter` 对表单进行修改
* `hook_form_FORM_ID_alter` 对表单进行修改

## `模块名.install`常用钩子：

* `hook_install` 模块安装后执行设置任务
* `hook_uninstall` 删除模块设置的信息
* `hook_schema` 定义数据库模型

## `模块名.views.inc`常用钩子：

* `hook_views_data_alter` 修改视图数据
