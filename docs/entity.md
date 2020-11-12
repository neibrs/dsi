Entity(实体)
============

Entity(实体)是持久存储信息的对象。对数据库的操作是通过实体相关类和函数完成的。

Entity分为ContentEntity(内容实体)和ConfigEntity(配置实体)。

ContentEntity(内容实体)可以有子类型，子类型称为bundle。

## 实体类型定义

通过操作界面了解核心提供的几个实体的功能：
* `node` /admin/content
* `node_type` /admin/structure/types
* `taxonomy` /admin/structure/taxonomy

技巧：查看系统定义的所有实体：
```bash
find -path '*/src/Entity/*'
```

实体类型是以[Plugin(插件)](plugin.md)的方式实现的。跟其他 plugin 一样，其定义在注释里。

有些实体类型的定义比较复杂，例如 node 实体类型：
```php
/**
 * Defines the node entity class.
 *
 * @ContentEntityType(                                // 插件类型：可以是`ContentEntityType`或`ConfigEntityType`
 *   id = "node",                                     // entity_type_id
 *   label = @Translation("Content"),                 // 实体名称。通过@Translation实现翻译
 *   label_collection = @Translation("Content"),      // 实体的复数名称
 *   label_singular = @Translation("content item"),
 *   label_plural = @Translation("content items"),
 *   label_count = @PluralTranslation(
 *     singular = "@count content item",
 *     plural = "@count content items"
 *   ),
 *   bundle_label = @Translation("Content type"),     // bundle的名称
 *   handlers = {                                     // 处理程序
 *     "storage" = "Drupal\node\NodeStorage",                 // 存储
 *     "storage_schema" = "Drupal\node\NodeStorageSchema",    // 元数据
 *     "view_builder" = "Drupal\node\NodeViewBuilder",        // 实体显示
 *     "access" = "Drupal\node\NodeAccessControlHandler",     // 访问控制
 *     "views_data" = "Drupal\node\NodeViewsData",            // 为视图提供数据
 *     "form" = {                                             // 表单
 *       "default" = "Drupal\node\NodeForm",                  // 缺省表单
 *       "delete" = "Drupal\node\Form\NodeDeleteForm",        // 删除确认表单
 *       "edit" = "Drupal\node\NodeForm",                     // 编辑表单
 *       "delete-multiple-confirm" = "Drupal\node\Form\DeleteMultiple"    // 批量删除表单
 *     },
 *     "route_provider" = {                                               // 路由提供器
 *       "html" = "Drupal\node\Entity\NodeRouteProvider",                 // HTML路由提供
 *     },
 *     "list_builder" = "Drupal\node\NodeListBuilder",                    // 列表构造
 *     "translation" = "Drupal\node\NodeTranslationHandler"               // 翻译
 *   },
 *   base_table = "node",                                                 // 基本表
 *   data_table = "node_field_data",                                      // 数据表
 *   revision_table = "node_revision",                                    // 版本表
 *   revision_data_table = "node_field_revision",                         // 版本数据表
 *   show_revision_ui = TRUE,                                             // 显示版本界面
 *   translatable = TRUE,                                                 // 可翻译
 *   list_cache_contexts = { "user.node_grants:view" },                   // 列表缓存上下文
 *   entity_keys = {              // 实体键定义
 *     "id" = "nid",              // ID字段
 *     "revision" = "vid",        // 版本字段
 *     "bundle" = "type",         // bundle字段
 *     "label" = "title",         // label字段
 *     "langcode" = "langcode",   // 语种
 *     "uuid" = "uuid",           // UUID
 *     "status" = "status",       // 状态字段
 *     "published" = "status",    // 发布状态字段
 *     "uid" = "uid",             // 用户ID
 *     "owner" = "uid",           // 拥有者字段
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   bundle_entity_type = "node_type",                      // bundle实体类型
 *   field_ui_base_route = "entity.node_type.edit_form",    // 字段配置基础route
 *   common_reference_target = TRUE,                        // 常用引用
 *   permission_granularity = "bundle",                     // 权限粒度
 *   links = {                                              // 链接模板
 *     "canonical" = "/node/{node}",
 *     "delete-form" = "/node/{node}/delete",
 *     "delete-multiple-form" = "/admin/content/node/delete",
 *     "edit-form" = "/node/{node}/edit",
 *     "version-history" = "/node/{node}/revisions",
 *     "revision" = "/node/{node}/revisions/{node_revision}/view",
 *     "create" = "/node",
 *   }
 * )
 */
class Node extends EditorialContentEntityBase implements NodeInterface {    // 实现该实体类型的类
  ......
}
```

有些实体类型无界面操作，定义非常简单。例如 action 实体类型定义：
```php
/**
 * Defines the configured action entity.
 *
 * @ConfigEntityType(                           // plugin 类型：配置实体类型
 *   id = "action",                             // entity_type_id
 *   label = @Translation("Action"),
 *   label_collection = @Translation("Actions"),
 *   label_singular = @Translation("action"),
 *   label_plural = @Translation("actions"),
 *   label_count = @PluralTranslation(
 *     singular = "@count action",
 *     plural = "@count actions",
 *   ),
 *   admin_permission = "administer actions",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "type",
 *     "plugin",
 *     "configuration",
 *   }
 * )
 */
class Action extends ConfigEntityBase implements ActionConfigEntityInterface, EntityWithPluginCollectionInterface {
```

## 字段

查看核心提供的字段类型: `core/lib/Drupal/Core/Field/Plugin/Field/FieldType/`
查看核心提供的表单器件类型: `core/lib/Drupal/Core/Field/Plugin/Field/FieldWidget/`
查看核心提供的显示格式类型: `core/lib/Drupal/Core/Field/Plugin/Field/FieldFormatter/`

查看系统提供的所有字段类型：
```bash
find -path '*/Plugin/Field/FieldType/*'
```

## 实体的路由

无需为Entity的添加、修改、显示、列表、删除提供routing设置，实体类型定义的route_provider已经自动提供路由定义:
```php
 *     "form" = {
 *       "default" = "Drupal\node\NodeForm",
 *       "delete" = "Drupal\node\Form\NodeDeleteForm",
 *       "edit" = "Drupal\node\NodeForm",
 *       "delete-multiple-confirm" = "Drupal\node\Form\DeleteMultiple"
 *     },
 *     "route_provider" = {                                               // 路由提供器
 *       "html" = "Drupal\node\Entity\NodeRouteProvider",                 // HTML路由提供
 *     },
 *     "list_builder" = "Drupal\node\NodeListBuilder",
 *     "translation" = "Drupal\node\NodeTranslationHandler"
 *   },
```

RouteProvider自动生成的路由：
`entity.实体名.collection` 列表
`entity.实体名.add_page` 添加界面
`entity.实体名.add_form` 添加表单
`entity.实体名.edit_form` 编辑表单
`entity.实体名.delete_form` 删除确认表单
`entity.实体名.canonical` 详情

route_provider的基类是\Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider，这个类的代码应该熟练掌握:
```php
  protected function getAddPageRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('add-page') && $entity_type->getKey('bundle')) {    // 定义了 bundle 键才生成 add_page 路由
      $route = new Route($entity_type->getLinkTemplate('add-page'));
      $route->setDefault('_controller', EntityController::class . '::addPage');           // 采用 EntityController 的控制器
      $route->setDefault('_title_callback', EntityController::class . '::addTitle');
      $route->setDefault('entity_type_id', $entity_type->id());                           // 提供 entity_type_id 参数给 EntityController
      $route->setRequirement('_entity_create_any_access', $entity_type->id());            // 采用 _entity_create_any_access

      return $route;
    }
  }
  
  protected function getAddFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('add-form')) {                    // 定义了链接模板才生成路由
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('add-form'));    // path 从链接模板里取
      // Use the add form handler, if available, otherwise default.
      $operation = 'default';
      if ($entity_type->getFormClass('add')) {                          // 检查实体类型是否定义了'add'表单处理程序
        $operation = 'add';
      }
      $route->setDefaults([
        '_entity_form' => "{$entity_type_id}.{$operation}",             // 采用 _entity_form
        'entity_type_id' => $entity_type_id,                            // 为 EntityController 的 _title_callback 提供参数
      ]);

      // If the entity has bundles, we can provide a bundle-specific title
      // and access requirements.
      $expected_parameter = $entity_type->getBundleEntityType() ?: $entity_type->getKey('bundle');
      // @todo: We have to check if a route contains a bundle in its path as
      //   test entities have inconsistent usage of "add-form" link templates.
      //   Fix it in https://www.drupal.org/node/2699959.
      if (($bundle_key = $entity_type->getKey('bundle')) && strpos($route->getPath(), '{' . $expected_parameter . '}') !== FALSE) {
        $route->setDefault('_title_callback', EntityController::class . '::addBundleTitle');
        // If the bundles are entities themselves, we can add parameter
        // information to the route options.
        if ($bundle_entity_type_id = $entity_type->getBundleEntityType()) {
          $bundle_entity_type = $this->entityTypeManager->getDefinition($bundle_entity_type_id);

          $route
            // The title callback uses the value of the bundle parameter to
            // fetch the respective bundle at runtime.
            ->setDefault('bundle_parameter', $bundle_entity_type_id)
            ->setRequirement('_entity_create_access', $entity_type_id . ':{' . $bundle_entity_type_id . '}');

          // Entity types with serial IDs can specify this in their route
          // requirements, improving the matching process.
          if ($this->getEntityTypeIdKeyType($bundle_entity_type) === 'integer') {
            $route->setRequirement($entity_type_id, '\d+');
          }

          $bundle_entity_parameter = ['type' => 'entity:' . $bundle_entity_type_id];
          if ($bundle_entity_type instanceof ConfigEntityTypeInterface) {
            // The add page might be displayed on an admin path. Even then, we
            // need to load configuration overrides so that, for example, the
            // bundle label gets translated correctly.
            // @see \Drupal\Core\ParamConverter\AdminPathConfigEntityConverter
            $bundle_entity_parameter['with_config_overrides'] = TRUE;
          }
          $route->setOption('parameters', [$bundle_entity_type_id => $bundle_entity_parameter]);
        }
        else {                                                          // 有些实体类型定义了 bundle 键但未定义 bundle_entity_type
          // If the bundles are not entities, the bundle key is used as the
          // route parameter name directly.
          $route
            ->setDefault('bundle_parameter', $bundle_key)
            ->setRequirement('_entity_create_access', $entity_type_id . ':{' . $bundle_key . '}');
        }
      }
      else {                                                            // 未定义 bundle 键的实体
        $route
          ->setDefault('_title_callback', EntityController::class . '::addTitle')
          ->setRequirement('_entity_create_access', $entity_type_id);   // _entity_create_access 无需传递 bundle
      }

      return $route;
    }
  }
  
  protected function getCanonicalRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('canonical') && $entity_type->hasViewBuilderClass()) {    // 有 view_bulder 处理程序才生成路由
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('canonical'));                           // path 从链接模板里取
      $route
        ->addDefaults([
          '_entity_view' => "{$entity_type_id}.full",                                           // 采用 _entity_view, 显示模式采用 full
          '_title_callback' => '\Drupal\Core\Entity\Controller\EntityController::title',
        ])
        ->setRequirement('_entity_access', "{$entity_type_id}.view")                            // 访问控制检查 view 操作
        ->setOption('parameters', [
          $entity_type_id => ['type' => 'entity:' . $entity_type_id],                           // 参数类型强制为entity类型
        ]);

      // Entity types with serial IDs can specify this in their route
      // requirements, improving the matching process.
      if ($this->getEntityTypeIdKeyType($entity_type) === 'integer') {
        $route->setRequirement($entity_type_id, '\d+');
      }
      return $route;
    }
  }
  
  protected function getEditFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('edit-form')) {                     // 有链接模板才生成路由
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('edit-form'));     // path 取链接模板的设置
      // Use the edit form handler, if available, otherwise default.
      $operation = 'default';
      if ($entity_type->getFormClass('edit')) {                           // 检查实体类型是否设置有 edit 表单处理程序
        $operation = 'edit';
      }
      $route
        ->setDefaults([
          '_entity_form' => "{$entity_type_id}.{$operation}",             // 采用 _entity_form
          '_title_callback' => '\Drupal\Core\Entity\Controller\EntityController::editTitle',
        ])
        ->setRequirement('_entity_access', "{$entity_type_id}.update")    // 访问控制检查 update 操作
        ->setOption('parameters', [
          $entity_type_id => ['type' => 'entity:' . $entity_type_id],     // 参数类型强制为 entity 类型
        ]);

      // Entity types with serial IDs can specify this in their route
      // requirements, improving the matching process.
      if ($this->getEntityTypeIdKeyType($entity_type) === 'integer') {
        $route->setRequirement($entity_type_id, '\d+');
      }
      return $route;
    }
  }
```

## 实体数据查询

根据实体ID查询一个实体数据：
```php
$entity_id = 1;
$entity = \Drupal::entityTypeManager()->getStorage('实体类型ID')->load($entity_id);
```

根据条件查询实体数据：
```php
$storage = \Drupal::entityTypeManager()->getStorage('实体类型ID');
$ids = $storage->getQuery()
  ->condition('字段名称', 值)
  ->condition('字段名称.子字段', 值)
  ->condition('字段名称.entity.字段值', 值)
  ->condition('字段名称.entity:实体类型ID.字段值', 值)
  ->execute();
$entities = $storage->loadMultiple($ids);
```
或
```php
$ids = \Drupal::entityQuery('实体类型ID')
  ->condition('字段名称', 值)
  ->condition('字段名称.子字段', 值)
  ->condition('字段名称.entity.字段值', 值)
  ->condition('字段名称.entity:实体类型ID.字段值', 值)
  ->execute();
```

## 实体显示

通过 ViewBuilder 对实体进行显示：
```php
\Drupal::entityTypeManager()->getViewBuilder('实体类型')->view(实体对象, 显示模式);
```
显示多个实体：
```php
\Drupal::entityTypeManager()->getViewBuilder('实体类型')->viewMultiple(实体对象数组, 显示模式);
```

## 实体模板

系统将自动采用模板ID为`实体ID`的显示模板，和模板ID为`实体ID_form`的表单模板。所以在`hook_theme`钩子定义的相应模板会被系统自动采用。

例如：
```php
function taxonomy_theme() {
  return [
    'taxonomy_term' => [                // 为实体类型 taxonomy_term 提供模板
      'render element' => 'elements',
    ],
  ];
}
```

通过 hook_theme_suggestions_HOOK 钩子实现模板替代：
```php
function node_theme_suggestions_node(array $variables) {
  $suggestions = [];                                                              // 准备要返回的替代建议数组
  $node = $variables['elements']['#node'];                                        // 从变量中获得实体对象
  $sanitized_view_mode = strtr($variables['elements']['#view_mode'], '.', '_');   // 对显示模式名称进行消毒

  $suggestions[] = 'node__' . $sanitized_view_mode;                               // 根据显示模式进行替代
  $suggestions[] = 'node__' . $node->bundle();                                    // 根据 bundle 进行替代
  $suggestions[] = 'node__' . $node->bundle() . '__' . $sanitized_view_mode;      // 根据 bundle + 显示模式组合进行替代
  $suggestions[] = 'node__' . $node->id();                                        // 根据实体ID进行替代
  $suggestions[] = 'node__' . $node->id() . '__' . $sanitized_view_mode;          // 根据实体ID + 显示模式组合进行替代

  return $suggestions;                                                            // 返回替代建议数组
}
```

## 参考

[官方文档](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Entity!entity.api.php/group/entity_api)
