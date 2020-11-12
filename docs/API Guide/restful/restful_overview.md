RESTful Web Services 模块概览
====================

Drupal 8 的RESTful是受Drupal 7[RESTful Web Services](https://www.drupal.org/project/restful)模块的启发，
对于序列化数据，它依赖于Drupal8核心的[Serialization](https://www.drupal.org/documentation/modules/serialization)模块。

## 什么是REST？
Web服务使其他应用程序可以通过Web读取和更新您站点上的信息。
REST是在您的站点上可使用Web服务的多种方法之一。与其他技术（如SOAP或XML-RPC）不同，
REST鼓励开发人员依赖HTTP方法（如GET和POST）对(Drupal管理的数据)资源进行操作。

如果您不熟悉REST，可以在[有关REST的详细信息](restful_more.md)部分中找到有关HTTP方法和其他REST主题（比如媒体类型和超媒体）的更多信息。

## 特性
这个模块构建在Drupal8的(Serialization)序列化模块之上，提供可定制的、可扩展的RESTful API数据。
开箱即用，它允许您与任何内容实体（node、user、comments…）或高于Drupal 8.2.0版本配置实体（词汇表、用户角色…）以及数据库日志条目等项目进行交互。

* 模块可以公开其他资源
* 支持get/post/patch/delete（不支持put）
* 自动与Drupal角色的认证系统集成：每个资源(和每个动词)一个权限
* 模块可以添加应用于任何资源的身份验证机制
* 模块可以添加更多的序列化格式-请参[阅序列化](https://www.drupal.org/documentation/modules/serialization)模块



## 其他信息
* [Serialization module: (de)serializing data to/from JSON & more](https://www.drupal.org/documentation/modules/serialization)
* [HAL module: serialization using the Hypertext Application Language](https://www.drupal.org/documentation/modules/hal)
* [Basic Auth module: HTTP Basic access authentication](https://www.drupal.org/documentation/modules/basic_auth)
* [RESTful Web Services API](https://www.drupal.org/developing/api/8/rest)
* [Serialization API](https://www.drupal.org/developing/api/8/serialization)
* Article: [An Introduction to RESTful Web Services in Drupal 8](https://drupalize.me/blog/201401/introduction-restful-web-services-drupal-8)
* DrupalCon Prague 2013 session: [REST and serialization in Drupal 8](https://prague2013.drupal.org/session/rest-and-serialization-drupal-8.html)
* Related contributed module: [API Rate Limiter](https://www.drupal.org/project/rate_limiter)
* Related contributed module: [Rest UI](https://www.drupal.org/project/restui)

