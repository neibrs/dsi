主题模板
========

在[Render数组](render_array.md)里，可以通过`#theme`使用主题模板。

## 定义主题模板

通过`hook_theme`[钩子](hook.md)定义自己的主题模板。例如：
```php
/**
 * Implements hook_theme().
 */
function node_theme() {
  return [
    'node' => [                             // 实体 node 的模板
      'render element' => 'elements',       // element变量名设置为elements
    ],
    'node_add_list' => [                    // 模板ID
      'variables' => ['content' => NULL],   // 设置模板参数
    ],
    'node_edit_form' => [                   // 模板ID
      'render element' => 'form',           // element变量名设置为form
    ],
    'field__node__title' => [               // 为node实体的title字段提供替代模板
      'base hook' => 'field',               // 基本模板为field
    ],
    'field__node__uid' => [                 // 为node实体的uid字段提供替代模板
      'base hook' => 'field',               // 基本模板为field
    ],
    'field__node__created' => [             // 为node实体的created字段提供替代模板
      'base hook' => 'field',               // 基本模板为field
    ],
  ];
}
```

技巧：查看系统中实现的所有`hook_theme`钩子：
```bash
find -name '*.module' | xargs grep hook_theme
```

## 编写twig模板

在模块的`templates`目录添加twig模板，文件名格式为`模板ID.html.twig`。

注意：如果模板ID含`_`，文件名必需将其替换为`-`。例如模板`employee_assignment`的模板文件名必需是`employee-assignment.html.twig`。

查看系统所有的模板文件：
```bash
find -path '*/templates/*.html.twig'
```

参考：
[Twig模板官方文档](https://www.drupal.org/docs/8/theming/twig)

## 为模板提供预处理函数

如果模板需要额外的变量，则需要预处理函数提供为其提供。

预处理函数名格式为`template_preprocess_模板ID`。例如：
```php
function template_preprocess_node(&$variables) {                                                      // 为node模板提供预处理函数
  $variables['view_mode'] = $variables['elements']['#view_mode'];                                     // 为模板提供view_mode变量
  // Provide a distinct $teaser boolean.
  $variables['teaser'] = $variables['view_mode'] == 'teaser';                                         // 为模板提供teaser变量
  $variables['node'] = $variables['elements']['#node'];                                               // 为模板提供node变量
  /** @var \Drupal\node\NodeInterface $node */
  $node = $variables['node'];
  $skip_custom_preprocessing = $node->getEntityType()->get('enable_base_field_custom_preprocess_skipping');

  // Make created, uid and title fields available separately. Skip this custom
  // preprocessing if the field display is configurable and skipping has been
  // enabled.
  // @todo https://www.drupal.org/project/drupal/issues/3015623
  //   In D9 delete this code and matching template lines. Using
  //   $variables['content'] is more flexible and consistent.
  $submitted_configurable = $node->getFieldDefinition('created')->isDisplayConfigurable('view') || $node->getFieldDefinition('uid')->isDisplayConfigurable('view');
  if (!$skip_custom_preprocessing || !$submitted_configurable) {
    $variables['date'] = \Drupal::service('renderer')->render($variables['elements']['created']);     // 为模板提供date变量
    unset($variables['elements']['created']);                                                         // 删除created内容
    $variables['author_name'] = \Drupal::service('renderer')->render($variables['elements']['uid']);  // 为模板提供author_name变量
    unset($variables['elements']['uid']);                                                             // 删除uid内容
  }

  if (!$skip_custom_preprocessing || !$node->getFieldDefinition('title')->isDisplayConfigurable('view')) {
    $variables['label'] = $variables['elements']['title'];                                            // 为模板提供label变量
    unset($variables['elements']['title']);                                                           // 删除title内容
  }

  $variables['url'] = !$node->isNew() ? $node->toUrl('canonical')->toString() : NULL;                 // 为模板提供url变量

  // The 'page' variable is set to TRUE in two occasions:
  //   - The view mode is 'full' and we are on the 'node.view' route.
  //   - The node is in preview and view mode is either 'full' or 'default'.
  $variables['page'] = ($variables['view_mode'] == 'full' && (node_is_page($node)) || (isset($node->in_preview) && in_array($node->preview_view_mode, ['full', 'default'])));

  // Helpful $content variable for templates.
  $variables += ['content' => []];
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];                                       // 将elements的子内容放入content变量
  }

  if (isset($variables['date'])) {
    // Display post information on certain node types. This only occurs if
    // custom preprocessing occurred for both of the created and uid fields.
    // @todo https://www.drupal.org/project/drupal/issues/3015623
    //   In D9 delete this code and matching template lines. Using a field
    //   formatter is more flexible and consistent.
    $node_type = $node->type->entity;
    // Used by RDF to add attributes around the author and date submitted.
    $variables['author_attributes'] = new Attribute();
    $variables['display_submitted'] = $node_type->displaySubmitted();
    if ($variables['display_submitted']) {
      if (theme_get_setting('features.node_user_picture')) {
        // To change user picture settings (e.g. image style), edit the
        // 'compact' view mode on the User entity. Note that the 'compact'
        // view mode might not be configured, so remember to always check the
        // theme setting first.
        $variables['author_picture'] = user_view($node->getOwner(), 'compact');
      }
    }
  }

  // Add article ARIA role.
  $variables['attributes']['role'] = 'article';                                                       // 为模板的attributes变量添加属性
}
```

技巧：查看系统实现的所有预处理函数：
```bash
grep template_preprocess_ . -r
```
