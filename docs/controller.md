Contronller（控制器）
====================

Controller是返回URL请求响应内容的函数。

[路由](routing.md)中由_controller定义的控制器函数需要在控制器类里实现。

技巧：查看系统所有的控制器类：
```
find -path '*/Controller/*'
```

一个简单的Hello world控制器：
```
  public function helloWorld() {
    return [
      '#markup' => $this->t('Hello world!'),
    ];
  }
```
该控制器返回的数组称为[Render数组](render_array.md)

## 控制器函数参数

## 控制器函数返回内容

控制器函数可返回两种类型的内容：
1. [Render数组](render_array.md)
2. Symfony Response对象
