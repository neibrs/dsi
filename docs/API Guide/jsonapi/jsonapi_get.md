JSON API 获取资源
=============

在下面的所有示例中，不需要请求头。如果匿名用户可以访问内容实体，则不需要身份验证。有关菜单等配置实体，请参阅最后一节。

注意，在所有情况下，当需要一个ID时，它总是实体的UUID，而不是实体ID。

## Accept header(接收头信息)
确保始终发送accept头：`Accept:application/vnd.api+json`
```
curl \
  --header 'Accept: application/vnd.api+json' \
  ....
```
## Basic GET example
URL: `http://example.com/jsonapi/node/article/{{article_uuid}}`

Response

HTTP 200响应。响应主体包含单个项目节点的json:api对象，包括属性、关系和链接(attributes, relationships, and links)。

## GET multiple articles(获取多个articles)
URL: `http://example.com/jsonapi/node/article`

Response

HTTP 200响应。响应主体包含最多50篇文章的json:api对象，包括到next的链接。

## GET first 10 articles(获取10条articles)
URL: `http://example.com/jsonapi/node/article?page[limit]=10`

Response

JSON response of max. 10 articles, including links to next.

## GET second page of 10 articles(获取第2页面的10条数据)
URL: `http://example.com/jsonapi/node/article?page[limit]=10&page[offset]=10`

Response

HTTP 200响应。响应主体包含最多10篇文章的json:api对象，包括指向prev、next等的链接。

有关分页的详细信息，请参阅：https://www.drupal.org/node/2803141

## GET multiple articles sorted(获取多条已排序的articles)
URL: `http://example.com/jsonapi/node/article?sort=nid`

Response
HTTP 200响应。响应主体包含项目的json:api对象，按nid升序排序。使用&sort=-nid按降序排序。

有关排序的详细信息，请参阅：https://www.drupal.org/node/2803141

## GET article filtered by title(获取按标题过滤的articles)
URL: `http://example.com/jsonapi/node/article?filter[article-title][path]=title&filter[article-title][value]={{title_filter}}&filter[article-title][operator]==`

Response
HTTP 200响应。响应主体包含文章的json:api对象，由匹配值“title filter”的“title”字段值过滤。

有关过滤器的详细信息，请参阅：https://www.drupal.org/node/2943641

## GET article media entity reference field_image url, uri by including references(通过包含引用获取文章媒体实体引用字段_image url，uri)
URL: `http://example.com/jsonapi/node/article/{{article_uuid}}?include=field_image,field_image.image,field_image.image.file--file&fields[field_image]=image&fields[file--file]=uri,url`

响应

HTTP 200响应。响应体包含包含媒体图像关系的json:api对象，通过'{{article_uuid}}'匹配单个文章节点

## GET article along with complete related data set (author, taxonomy term, etc.)(将文章与完整的相关数据集（作者、分类术语等）放在一起)
URL: `http://example.com/jsonapi/node/article?fields[node--article]=uid,title,created&include=uid`

Response

HTTP 200响应。响应主体包括一个包含有关关联用户的所有信息的用户对象。同样的语法也适用于其他相关数据，如分类术语。


GET article along with selected related data items (author, taxonomy term, etc.)(将文章与选定的相关数据项（作者、分类术语等）放在一起)
URL: `http://example.com/jsonapi/node/article?fields[node--article]=uid,title,created&fields[user--user]=name,mail&include=uid`

Response

HTTP 200响应。响应主体包括一个用户对象，其中包含相关对象中的指定字段（在本例中是作者姓名和作者电子邮件）。同样的语法也适用于其他相关数据，如分类术语。

## GET user accounts
URL: `http://example.com/jsonapi/user/user?filter[anon][condition][path]=uid&filter[anon][condition][value]=0&filter[anon][condition][operator]=<>`

Response

HTTP 200响应。响应主体包含系统中用户帐户的json:api对象，不包括匿名用户帐户。

请注意，如果要获取所有用户帐户的列表，则必须使用上述查询，因为向/jsonapi/user/user发出get请求将导致HTTP 403错误。


## Getting config entities.(获取配置实体)
由于配置实体（菜单、节点类型、tour）不等于内容实体（节点、用户），所以这有点复杂。当前配置实体是只读的。

为了便于测试，我们使用了user-1和基本auth模块。

启用基本身份验证模块
假设用户名为admin，密码为admin
使用以下命令将列出菜单。
```
curl \
  --header 'Accept: application/vnd.api+json' \
  http://admin:admin@drupal.d8/jsonapi/menu/menu
```