JSON API 删除资源
=============
## Authentication(认证)
通常，某些形式的身份验证用于删除请求。下面的示例都使用基本身份验证。

启用HTTP基本身份验证模块，为API用户（和角色）设置权限，并将“设置编码的用户名和密码”添加到“授权”请求头。

本页上的示例头要求Drupal用户“api”密码“api”。此用户必须具有删除请求内容的权限。

## Headers(报头)
所有GET请求都需要使用下面的头信息来获得适当的JSON:API请求和响应。

* Accept: application/vnd.api+json
* Authorization:Basic YXBpOmFwaQ==

## 基本的删除请求地址
URL: `http://localhost:8181/jsonapi/node/article/{{article_uuid}}`

Response

HTTP 204 (No content) response. Empty response body.

The article {{article_uuid}} is now deleted.