
JSON API 更新浆糊(PATCH)
====================

## Authentication(认证)
通常，某些形式的身份验证用于更新资源请求。

下面的示例都使用基本身份验证。启用HTTP基本身份验证模块，为API用户（和角色）设置权限，并将“设置编码的用户名和密码”添加到“授权”请求头。

本页上的示例头要求Drupal用户“api”密码“api”。此用户必须具有删除请求内容的权限。

## Headers(报头)
所有GET请求都需要使用下面的头信息来获得适当的JSON:API请求和响应。

* Accept: application/vnd.api+json
* Authorization:Basic YXBpOmFwaQ==

## Basic PATCH request
URL: `http://example.com/jsonapi/node/article/{{article_uuid}}`

**Request body**
```
{
  "data": {
    "type": "node--article",
    "id": "{{article_uuid}}",
    "attributes": {
      "title": "My updated title"
    }
  }
}
```
`id`是必须的，`attributes`是需要被更新的内容。

Response

HTTP 200 response. The response body with the JsonApi response of the updated entity.

## PATCH with more attributes(更新多个资源)
URL: `http://example.com/jsonapi/node/article/{{article_uuid}}`
```
{
  "data": {
    "type": "node--article",
    "id": "{{article_uuid}}",
    "attributes": {
      "title": "My updated title",
      "body": {
        "value": "Updated body text",
        "format": "plain_text",
        "summary": "Updated summary"
      }
    },
    "relationships": {
      "uid": {
        "data": {
          "type": "user--user",
          "id": "{{user_uuid}}"
        }
      }
    }
  }
}
```

Response

HTTP 200 response. The response body with updated body, summary and author updated entity.