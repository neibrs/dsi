JSON API 排序
===========

json:api使在一个请求中检索多个资源成为可能，这些路由称为“集合”路由。

可以通过向基本资源路由发送get请求来获取资源集合，例如`Get /jsonapi/node/article`，而不包括`uuid`。默认情况下，这将包括指定类型的所有资源。

默认情况下，筛选和排序在所有标准资源上都可用。

## Headers(报头)
所有GET请求都需要使用下面的头信息来获得适当的JSON:API请求和响应。

* Accept: application/vnd.api+json
* Content-Type: application/vnd.api+json

下面的所有示例都需要下面的信息:
`Authorization: Basic YXBpOmFwaQ==`

## Sorting collections(集合排序)
#### Sorting by 'created'(按创建时间排序)
```
SHORT
sort=created

NORMAL
sort[sort-created][path]=created
```

#### Sort by author's username
<i>注意前面的“减号”符号（-）指定降序。</i>
```
SHORT
sort=-uid.name

NORMAL
sort[sort-author][path]=uid.name
sort[sort-author][direction]=DESC
```

#### Sort by multiple fields
```
SHORT
sort=-created,uid.name

NORMAL
sort[sort-created][path]=created
sort[sort-created][direction]=DESC
sort[sort-author][path]=uid.name
```
