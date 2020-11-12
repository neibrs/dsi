Views(视图)
===========

Views(视图)是一个通用的查询和显示引擎。

为模块提供视图配置的方法为：
1. 浏览器访问`admin/structure/views`视图配置界面添加和配置视图。
2. 导出视图配置文件到模块的`config/install`目录。
3. 编辑视图配置文件。技巧：通过gvimdiff与node模块的views.view.content.yml进行比较编辑。

视图配置文件编辑常犯的错误：很多无用的配置信息未删除。

## 视图插件

见 [视图插件](views_plugin.md)

## 提供数据

查看各模块如何为视图提供数据：
```bash
find -name '*ViewsData.php'
```

例如NodeViewsData：
```php
    $data['node_field_data']['type']['argument']['id'] = 'node_type';   // 将type字段的argument插件替换为node_type
    
    $data['node_field_data']['status']['filter']['label'] = $this->t('Published status');
    $data['node_field_data']['status']['filter']['type'] = 'yes-no';    // 将status字段的filter插件是BooleanOperator,将插件的type属性设置为yes-no类型
    // Use status = 1 instead of status <> 0 in WHERE statement.
    $data['node_field_data']['status']['filter']['use_equal'] = TRUE;    
```

例如CommentViewsData:
```php
    $entities_types = \Drupal::entityManager()->getDefinitions();               // 获得系统所有的实体类型

    // Provide a relationship for each entity type except comment.
    foreach ($entities_types as $type => $entity_type) {
      if ($type == 'comment' || !$entity_type->entityClassImplements(ContentEntityInterface::class) || !$entity_type->getBaseTable()) {
        continue;
      }
      if ($fields = \Drupal::service('comment.manager')->getFields($type)) {    // 如果该实体类型有comment字段
        $data['comment_field_data'][$type] = [                                  // 为comment_field_data提供到该实体类型的关系
          'relationship' => [
            'title' => $entity_type->getLabel(),                                // 实体类型的标题
            'help' => $this->t('The @entity_type to which the comment is a reply to.', ['@entity_type' => $entity_type->getLabel()]),
            'base' => $entity_type->getDataTable() ?: $entity_type->getBaseTable(),   // 实体类型的表名
            'base field' => $entity_type->getKey('id'),                               // 实体类型的id字段
            'relationship field' => 'entity_id',                                      // 关联到comment_field_data的entity_id字段
            'id' => 'standard',                                                       // 采用标准的standard插件
            'label' => $entity_type->getLabel(),                                      // 实体类型的名称
            'extra' => [                                                              // 为关系提供额外的条件
              [
                'field' => 'entity_type',
                'value' => $type,
                'table' => 'comment_field_data',
              ],
            ],
          ],
        ];
      }
    }
```

## 参考

[官方文档](https://api.drupal.org/api/drupal/core!modules!views!views.api.php/group/views_overview)
