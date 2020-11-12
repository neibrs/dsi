JSON API 概览
===========

json:api模块提供的API以Drupal的实体类型和bundle为中心。每个bundle都会收到自己独特的URL路径，这些路径都遵循共享模式。

与drupal core rest模块不同，这些路径是不可配置的，默认情况下都是启用的。

与core rest不同，json:api不仅仅是一种像json或hal+json这样的格式。它包含了一套更广泛的关于API如何工作的规则。它规定了应该使用哪些HTTP方法、在特定情况下应该返回哪些HTTP响应代码、响应主体的格式以及资源之间的链接。

## Type(类型)
json:api中的每个资源都必须具有全局唯一的类型属性。

drupal json:api实现从实体类型machine name和bundle machine name派生此类型属性。

例如，文章、页面和用户分别被赋予类型node——article、node--page和user--user。

请注意，drupal中的用户实体类型没有捆绑包。当实体类型没有bundle时，只需重复实体类型以保持一致性。

## URL Structure
JSON:API URL像这样:
```
GET|POST     /jsonapi/node/article
PATCH|DELETE /jsonapi/node/article/{uuid}
```
在API中，每个资源类型都必须是唯一可寻址的。这意味着API可用的每个类型都必须具有唯一的URL。除了要求每个类型都是可寻址的之外，这通常意味着只有一个资源类型可以获取给定的URL。Drupal实现遵循以下模式：

`/jsonapi/{entity_type_id}/{bundle_id}[/{entity_id}]`.

URL通常以jsonapi为前缀.

之后，实体类型ID和bundle ID用/连接起来。请注意，在`/jsonapi/node`处没有URL，这是因为该URL将通过从单个URL提供多个资源类型（由于多个bundle类型）来是违反规范的。
```
Exists:
/jsonapi/node/page
/jsonapi/node/article

Does not exist:
/jsonapi/node
```

在实体类型和bundle ID之后，有一个可选的ID部分。要寻址单个资源，要么获取、更新或删除它们，必须包含此路径部分。它始终是资源的UUID。当创建一个具有或不具有ID的新资源，或获取一个单一类型的资源集合时，可以省略ID路径部分。
```
GET, POST
/jsonapi/node/article

PATCH, DELETE
/jsonapi/node/article/{uuid}
```

## HTTP Methods
json:api指定要接受的HTTP方法。它们是：获取、发布、修补和删除。值得注意的是，PUT不包括在内。
* GET - Retrieve data, can be a collection of resources or an individual resource
* POST - Create a new resource
* PATCH - Update an existing resource
* DELETE - Remove an existing resource

## Request headers
请确保在适当时使用“Content type”和“Accept”头。有关更多详细信息，请参阅[客户职责](http://jsonapi.org/format/#content-negotiation-clients）
```
Accept: application/vnd.api+json
Content-Type: application/vnd.api+json
```
## Response Codes
json:api规范还规定了可接受的响应。Drupal实现使用其中的一个子集。模块可以用以下代码响应：
* 200 OK-所有成功的获取和修补请求
* 201 Created-所有成功的POST请求（响应包括新创建的资源）
* 204 No Content-所有成功的删除请求