Form(表单)
==========

开发表单有两个步骤：
1. 在模块的`src/Form`目录添加表单类
2. 在`模块名.routing.yml`里通过`_form`使用该表单类

技巧：查看系统的所有表单类：
```bash
find -path '*/src/Form/*'
```
技巧：查看Routing中`_form`的使用：
```bash
find -name '*.routing.yml' | xargs grep '_form'
```

## 表单类

表单类一般从`FormBase`派生，例如：
```php
class UserLoginForm extends FormBase {
```

还可以从其他基类派生：
* `ConfigFormBase` 配置编辑表单
* `ConfirmFormBase` 确认表单
* `FormBase` 普通表单

技巧：查看系统用到的所有表单基类：
```bash
find -path '*/src/Form/*' | xargs grep 'extends'
```

从`FormInterface`这个接口可以看出，表单类有3个需要实现的重要方法：
1. `buildForm` 构造表单界面，返回Render数组
2. `validateForm` 对用户录入信息进行校验
3. `submitForm` 对用户录入信息进行处理，例如持久化到数据库

submitForm例子：
```php
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $account = $this->userStorage->load($form_state->get('uid'));   // 通过`$form_state->get`获得用户录入数据

    // A destination was set, probably on an exception controller,
    if (!$this->getRequest()->request->has('destination')) {
      $form_state->setRedirect(                                     // 通过`$form_state->setRedirect`实现界面跳转
        'entity.user.canonical',
        ['user' => $account->id()]
      );
    }
    else {
      $this->getRequest()->query->set('destination', $this->getRequest()->request->get('destination'));
    }

    user_login_finalize($account);
  }
```

## 在代码里显示表单：
```php
$build = \Drupal::service('form_builder')->getForm('表单类', 参数...);
```

## 参考
[官方文档](https://api.drupal.org/api/drupal/core!core.api.php/group/form_api)
