JSON API GET, POST, PATCH and DELETE
====================================

本节包含以下每个请求类型的示例和信息：
* [获取资源(GET)](jsonapi/jsonapi_get.md)
* [创建资源(POST)](jsonapi/jsonapi_post.md)
* [更新资源(PATCH)](jsonapi/jsonapi_patch.md)
* [删除资源(DELETE)](jsonapi/jsonapi_delete.md)

所有示例都是可在浏览器或JSON客户端（例如：postman）中工作的示例。
您需要一个标准的Drupal安装网站、许多nodes文章和启用json:api模块。

`http://localhost:8181/jsonapi/node/article/{{article_uuid}}`


所有请求都使用'/jsonapi'路径为前缀，这是json:api模块的默认值。
在各种示例中，占位符由双大括号语法表示，例如{{…}}。将它们替换为适用于您的环境的指定数据。
