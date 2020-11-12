JSON API 创建资源(POST)
===================
此页面显示了json:api模块的各种POST请求的示例。

Post请求用于创建新资源。如果需要修改资源，则需要对其进行PATCH操作。

json:api规范（因此json:api模块）只支持为每个post请求创建一个资源。对于Drupal，这意味着不可能在一个请求中创建多个实体（仅此模块）。

如果希望与引用的实体同时创建，则可能需要执行此操作。虽然json:api不能支持这种行为，但是[子请求](https://www.drupal.org/project/subrequests)这样的模块可以帮助满足这些需求。

## Authentication(认证)
通常，使用POST请求来进行某种形式的身份验证。下面的示例都使用基本身份验证。启用HTTP基本身份验证模块，为API用户（和角色）设置权限，并将编码的用户名和密码设置为“授权”请求头。

此页前面的示例头信息要求Drupal用户“api”和密码“api”。此用户必须具有创建给定内容的权限。

## Headers(报头)
所有GET请求都需要使用下面的头信息来获得适当的JSON:API请求和响应。

* Accept: application/vnd.api+json
* Content-Type: application/vnd.api+json

还需要下面的信息:
`Authorization: Basic YXBpOmFwaQ==`

## Curl
假设数据在`payload.json`文件中:
```
curl \
    --user api:api \
    --header 'Accept: application/vnd.api+json' \
    --header 'Content-type: application/vnd.api+json' \
    --request POST http://drupal.d8/jsonapi/node/article \
    --data-binary @payload.json
```

## Basic POST request(基础POST请求)
URL: `http://example.com/jsonapi/node/article`
Request body:
```
{
  "data": {
    "type": "node--article",
    "attributes": {
      "title": "My custom title",
      "body": {
        "value": "Custom value",
        "format": "plain_text"
      }
    }
  }
}
```

Response

HTTP 201 (Created) response. The response body contains the JsonApi response of the created entity.

## POST request with relationships(含有关系的POST请求)
Request body:
```
{
  "data": {
    "type": "node--article",
    "attributes": {
      "title": "Article by admin",
      "body": {
        "value": "Custom value",
        "format": "plain_text"
      }
    },
    "relationships": {
      "uid": {
        "data": {
          "type": "user--user",
          "id": "{{UUID of user 1}}"
        }
      }
    }
  }
}
```

Response

HTTP 201 (Created) response. The response body contains the JsonApi response of the created entity.