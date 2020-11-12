entity_form(实体表单)
====================

需先了解： [Form(表单)](form.md)

entity_form的作用：为实体提供添加和编辑界面。

## 表单界面配置方法

* 实体的`baseFieldDefinitions`函数为各字段配置显示方式
* 其他模块可通过`hook_entity_base_field_info_alter`钩子修改`baseFieldDefinitions`定义的字段
* 可通过`Manage form display`界面配置表单显示

## 代码显示实体表单
通过 EntityFormBuilder::getForm 获得表单的 render 数组：
```php
\Drupal::service('entity.form_builder')->getForm(实体对象);
```
参考：NodeController::add()
