REST配置和REST请求基础的入门知识
====================
* 配置
    请先阅读[RESTful Web Services API — Practical](restful_api_overview.md#practical)

    现在你应该已经如何做下列几项内容了吧。
    * 暴露指定数据作为REST资源
    * 授权
    * 自定义REST资源格式(JSON, XML, HAL+JSON, CSV...)
    * 自定义REST资源的授权机制(cookie, OAuth, OAuth2.0 Token Bearer, HTTP基础认证....)

    有了这些知识，您可以配置一个Drupal8站点来公开数据，以精确地满足您的需求。

* REST请求基础

  * 安全与不安全的方法
  >  REST使用HTTP和HTTP动作。
  >  HTTP动作(也称为请求方法)包括: `GET`, `HEAD`, `POST`, `PUT`, `DELETE`, `TRACE`, `OPTIONS`, `CONNECT`和`PATCH`。
  >
  >  一些是安全的方式，因为它们只读。因此它们对网站存储的数据无害。这些方法包括: `HEAD`, `GET`, `OPTIONS`和`TRACE`。
  >
  > 其他方法不是安全方法，因为它们会执行写操作，操作存储的数据。
  >
  >  好消息是: `PUT`方法不再被支持.
  * 不安全方法与CSRF保护: X-CSRF-Token请求头信息
  > Drupal8通过在使用非安全方法时要求发送`X-CSRF-Token`请求头来保护其REST资源免受CSRF攻击。因此，在执行非只读请求时，需要该令牌。
    这样的令牌可以在/session/token上检索。

  * 格式化
  > 在执行REST请求时，必须通知Drupal你正在使用的序列化格式(即使给定的REST资源只支持一种格式)。所以：
  >> * 总是指定一个`?_format`查询字符串，例如: `http://localhost:8181/node/1?_format=json`
  >> * 在这个格式中发送一个包含数据的请求时，必须指定一个`Content-Type`请求头，这种用例场景用于`POST`和`PATCH`。
  >
  >注意：由于浏览器和代理不支持Drupal8，所以从Drupal8中删除了基于头部的内容协商。