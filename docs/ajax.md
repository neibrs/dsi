AJAX
======

参考：
[Ajax API](https://api.drupal.org/api/drupal/core!core.api.php/group/ajax)
[AJAX API](https://www.drupal.org/docs/8/api/ajax-api)
[AJAX Forms](https://www.drupal.org/docs/8/api/javascript-api/ajax-forms)

## 表单元素`#state`属性

参考: [Conditional Form Fields](https://www.drupal.org/docs/8/api/form-api/conditional-form-fields)

## 表单元素`#ajax`属性

例如BlockForm:
```php
      $form['theme'] = [
        '#type' => 'select',
        '#options' => $theme_options,
        '#title' => t('Theme'),
        '#default_value' => $theme,
        '#ajax' => [
          'callback' => '::themeSwitch',              // ajax事件触发后回调该函数
          'wrapper' => 'edit-block-region-wrapper',   // 要处理的HTML元素ID
        ],
      ];
```
