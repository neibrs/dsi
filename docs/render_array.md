Render数组
==========

Render数组是嵌套数组，它包括要显示的数据，以及如何显示这些数据的属性。

有三类最常用的Render数组：#markup类、#type类、#theme类。

## #markup类render数组：
```
    if (count($results)) {
      $build['search_results_title'] = [
        '#markup' => '<h2>' . $this->t('Search results') . '</h2>',   // 通过#markup提供HTML内容
      ];
    }
```

## #type类render数组：
```
    $form['name'] = [
      '#title' => t('Name'),              // render element的标题
      '#type' => 'textfield',             // render element的类型，这里的textfield代表文本录入框
      '#default_value' => $type->label(), // 录入框显示的缺省值
      '#description' => t('The human-readable name of this content type.'), // 帮助信息
      '#required' => TRUE,                // 是否必录项
      '#size' => 30,
    ];
```
在这类render数组中，#type是render element的类型，其他是为该render element提供的属性。

技巧：在core/lib/Drupal/Core/Render/Element目录可以核心提供的所有render element类型。看这些代码可以了解到不同element类型可设置的属性。

注意：所有核心提供的render element类型都必须熟悉。

## #theme类render数组：
```
      $current_theme['operations'] = [
        '#theme' => 'links',                      // 指定#theme类型
        '#links' => $theme->operations,           // 要显示的链接数组
        '#attributes' => [                        // 为HTML标签提供属性
          'class' => ['operations', 'clearfix'],  // 为HTML标签提供class属性
        ],
      ];

```
技巧：查看core/includes/theme.inc文件的drupal_common_theme()函数可以获得核心提供的所有常用的#theme。

最常用的#theme包括：
* `links` 一串链接
* `table` 表格
* `item_list` 项目列表

技巧：查看table的用法
```
grep '#theme' . -r | grep table
```

## Render数组的常用属性：

* `#attached` 添加CSS, JS, feeds, HTTP headers, meta tags等附件。

首先在`模块名.libraries.yml`定义各library包含的CSS和JS：
```yaml
drupal.node:                    # 库ID
  version: VERSION
  css:
    layout:
      css/node.module.css: {}   # 添加CSS文件
  js:
    node.js: {}                 # 添加JS文件
  dependencies:
    - core/drupal.entity-form
    - core/drupalSettings
```
然后在render数组里附加library:
```php
    $form['author'] = [
      '#type' => 'details',
      '#title' => t('Authoring information'),
      '#group' => 'advanced',
      '#attributes' => [
        'class' => ['node-form-author'],
      ],
      '#attached' => [                        // 向输出添加附件
        'library' => ['node/drupal.node'],    // 添加libarary里定义的CSS和JS。library格式为`模块名称/库ID`
      ],
      '#weight' => 90,
      '#optional' => TRUE,
    ];

```

## 参考

[官方文档](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Render!theme.api.php/group/theme_render)
