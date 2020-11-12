JSON:API和核心REST模块的对比
====================

差别:
* core的rest模块允许任何东西（任何格式、任何逻辑、任何HTTP方法）和极端的配置。强大但复杂，因此相对脆弱。
* JSON:API专注于以一致的方式公开Drupal最大的优势（实体/数据模型）。对于大多数用例来说，简单但足够强大。
特征矩阵:

| 特征   |      JSON:API      |  REST |  描述 |
|----------|:-------------:|--------------:|----------------:|
| 暴露实体资源   |  Y | Y | REST: 需要对每个实体进行设置, JSON:API默认全部实体暴露,两者都有权限较验|
| 自定义暴露资源 |   |   Y |编写自定义的@RestResource插件，JSON:API仅支持实体|
| 获取独立的资源 | Y |    Y |
| 获取资源列表   | Y | kinda | REST:需要配置一个视图(view),并且生成(set up) "REST export"显示页面(display)|
| 资源分页      | Y | | REST: 不支持! REST导出视图返回的是所有资源|
| 资源过滤      | Y | kinda | REST: 仅当为每个字段和每个可能的运算符创建公开的筛选器时|
| 资源排序      | Y |       |   |
| 包含/嵌入     | Y | 仅HAL+JSON    | |
| 不需要对字段值进行不必要的包装| Y | | HAL规范化和默认规范化（以及所有格式）都会暴露Drupal使用的内存中的PHP数据结构，从而给用户带来痛苦的体验。JSON:API简化了单个基数和单个属性字段的规范化。|
| 可以删除用户不必要的字段 | Y | | |
| 一致的URLs | Y | | |
| 用户可以发现可用的资源类型 | Y | | |
| Drupal不知响应结构 | Y | | REST: the HAL normalization in theory is void of Drupalisms, but in practice it isn't.|
| 客户端库 | Y | | |
| 可扩展的规格| [WIP](http://jsonapi.org/extensions/) | | |
| 零配置 | Y | | REST:每个@RestResource插件定义都可以公开，但必须配置为公开。对于每个方法，必须选择允许的格式、允许的身份验证提供程序，甚至可以选择允许的HTTP方法。
            JSON:API所有实体都是自动公开的，实体/字段访问是受尊重的，所有已安装的身份验证提供程序都是自动允许的。|

更多信息:
查看[Drupal核心添加json:api模块的基本原理](https://www.drupal.org/project/ideas/issues/2836165)和[模块架构基本原理](https://cgit.drupalcode.org/jsonapi/tree/jsonapi.api.php)