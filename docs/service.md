service(服务)
============

通过`模块.services.yml`定义服务:
```yaml
services:
  服务ID:
    class: 提供服务的类
  服务ID2:
    class: 提供服务的类2
    arguments: ['参数1', '参数2', ...]
```

调用服务的函数：
```php
\Drupal::service('服务ID')->函数名();
```
