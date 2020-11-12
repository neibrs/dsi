Route(路由)
============

Route(路由)定义了系统如何响应URL请求。

可在模块目录的"模块名称.routing.yml"文件定义路由。参考core/module/node/node.routing.yml：
```yaml
system.admin_structure:     # 路由ID
  path: '/admin/structure'  # 要响应的URL地址
  defaults:                 # 处理方法
    _controller: '\Drupal\system\Controller\SystemController::systemAdminMenuBlockPage' # 处理函数
    _title: 'Structure'                                                                 # 标题
  requirements:             # 访问权限
    _permission: 'access administration pages'  # 访问权限
```

技巧: 查看系统的所有Routing定义文件
```bash
find -name '*.routing.yml'
```

## 处理方法

* _controller: 其值为`回调函数`，例如：`类::函数`或`服务:函数`
* _entity_form: 其值为`实体名称.操作名称`
* _form：其值为`表单类名称`

技巧: 查看_entity_form的定义方法
```bash
find -name '*.routing.yml' | xargs grep _entity_form
```

## 访问权限

* _permission: 其值为`权限ID`
* _entity_access: 其值为`实体名称.操作名称`
* _entity_create_any_access: 其值为`实体名称`
* _entity_create_access: 其值为`实体名称.bundle名称`

技巧: 查看_entity_access的定义方法
```bash
find -name '*.routing.yml' | xargs grep _entity_access
```
