Javascript 和 Drupal 8 RESTful web 服务
===============

本页旨在概述如何使用JavaScript与Drupal8的RESTful服务进行通信。它按实体类型分为多个部分。

大纲:
1. 在何总URL上使用何总HTTP方法(GET, POST, PATCH, DELETE)
2. 发送什么头
3. 应该发送什么数据
4. 服务器响应的数据是否是期望的

下面一些实体类型:
* Users
* Nodes
* Comments
* Vocabularies
* Terms

## Users
#### Login
POST: `https://example.com/user/login?_format=json`
Content-type: application/json

```
{
"name": "admin",
"pass": "password"
}
```
200OK

#### Logout
POST `http://example.com/user/logout?_format=json`
Content-type: application/json
200 - OK

#### 获取
GET `http://example.com/user/1?_format=json`
None
200 - OK

#### Register
POST: `https://example.com/user/register?_format=json`
Content-type: application/json
```
{
"name": { "value": "fooBar" },
"mail": { "value": "foo@bar.com" },
"pass": { "value": "secretSauce" }
}
```
200 OK

## Nodes
#### Create
POST: `http://example.com/entity/node`
Content-type: application/json
```
{
  "type":[{"target_id":"article"}],
  "title":[{"value":"Hello World"}],
  "body":[{"value":"How are you?"}]
}
```
201 - Created

For setting the value of an entity reference field referencing another entity type, all you need is it's uuid:
```
"_embedded": {
"https://example.com/rest/relation/node/article/my_entity_reference_field":
  [{ 
  "uuid":[{"value":"yourUUID-xxx-xxxx-xxxx-xxxxxxxxx"}]
  }]
}
```

#### Retrieve

GET: `http://example.com/node/123?_format=json`
Content-type: *
Accept: application/json
200 - OK

#### Update

PATCH: `http://example.com/node/123`
Content-type: application/json
```
{
  "nid":[{"value":"123"}],
  "type":[{"target_id":"article"}],
  "title":[{"value":"Goodbye World"}]
}
```
204 - No Content

#### Delete

DELETE: `http://example.com/node/123`
Content-type: *
```{
"type":
  [{"target_id":"article"}]
}
```
204 - No Content

其他(Comment, Taxonomy)请参见[这里](https://www.drupal.org/docs/8/core/modules/rest/javascript-and-drupal-8-restful-web-services)

## 使用jQuery来操作RESTful Drupal 8 CRUD

#### 创建
```$xslt
var package = {}
package.title = [{'value':'t1'}]
package.body = [{'value':'b1'}]
package._links = {"type":{"href":"http://local.drupal8.org/rest/type/node/page"}}

$.ajax({
  url: "http://example.com/entity/node",
  method: "POST",
  data: JSON.stringify(package),
  headers: {
    "Accept": "application/json",
    "Content-Type": "application/hal+json"
  },
  success: function(data, status, xhr) {
    debugger
  }
})
```

#### 获取
```$xslt
$.ajax({
  url: "http://example.com/node/3?_format=hal_json",
  method: "GET",
  headers: {
    "Content-Type": "application/hal+json"
  },
  success: function(data, status, xhr) {
    debugger
  }
})
```

#### 获取并更新
```$xslt
$.ajax({
  url: "http://example.com/node/3?_format=hal_json",
  method: "GET",
  headers: {
    "Content-Type": "application/hal+json"
  },
  success: function(data, status, xhr) {
    var package = {}
    package.title = data.title
    package.body = data.body
    package.title[0].value = 'yar'
    package._links = {"type":{"href":"http://example.com/rest/type/node/page"}}
    debugger

    $.ajax({
      url: "http://example.com/node/3",
      method: "PATCH",
      data: JSON.stringify(package),
      headers: {
        "X-CSRF-Token": "niCxgd5ZZG25YepbYtckCy7Q2_GL2SvMUY5PINxRAHw",
        "Accept": "application/json",
        "Content-Type": "application/hal+json"
      },
      success: function(data, status, xhr) {
        debugger
      }
    })

  }
})
```

#### 删除
```$xslt
$.ajax({
  url: "http://example.com/node/3",
  method: "DELETE",
  headers: {
    "Accept": "application/json",
    "Content-Type": "application/hal+json"
  },
  success: function(data, status, xhr) {
    debugger
  }
})
```

## 从GET获取的数据自动被缓存
Drupal 8 rest会自动缓存所有请求过的数据，如果你不想使用此缓存，要么清除缓存，要么在请求地址后面添加一个时间点。

如:`node/123?_format=json&time=123456789`

或者创建一个自定义的资源，使用`addCacheableDependency()`到`ResourceResponse`中:
```$xslt
$response = new ResourceResponse(array('hello' => 'world'));
$response->addCacheableDependency($account);
return $response;
```

## 相关资源或模块
* [jDrupal](https://www.drupal.org/project/jDrupal)