JSON API 包含
===========

TODO: 使用类似的查询字符串`?include=field_comments.uid`包含`field_comments`引用的所有实体和`uid`在所有实体中引用了`uid`的实体;

json:api允许您指定希望包含在响应文档中的路径关系，从而帮助您消除HTTP请求。怎么用？

假设您有一篇文章有两条评论，每条评论都有相同的作者。要获取所有数据而不使用`include`，您首先请求获取`/jsonapi/node/article/some-random-uuid`:
```
{
  "data": {
    "type": "node--article",
    "id": "some-random-uuid",
    "relationships": {
      "field_comments: {
        "links": {
          "related": {
            "href": "https://my.example.com/node/article/some-random-uuid/field_comments"
          }
        }
      }
    }
  }
}
```
然后请求`GET /node/article/some-random-uuid/field_comments`:
```
{
  "data": [{
    "type": "comment",
    "id": "one-random-uuid",
    "relationships": {
      "uid: {
        "links": {
          "related": {
            "href": "https://my.example.com/comment/one-random-uuid/uid"
          }
        }
      }
    }
  }, {
    "type": "comment",
    "id": "two-random-uuid",
    "relationships": {
      "uid: {
        "links": {
          "related": {
            "href": "https://my.example.com/comment/two-random-uuid/uid"
          }
        }
      }
    }
  }
}
```
第三步，你需要执行两个或多个请求`/comment/one-random-uuid/uid`和`/comment/two-random-uuid/uid`

我们可以看到第二个请求是完全不必要的，因为我们知道这两个注释的作者在我们的示例中是相同的。

那么，如何包括帮助呢？

这很容易！只需在原始请求URL中添加一个查询参数以及您希望包含的关系字段的名称，服务器就会知道要查找所有内容并将其添加到原始响应文档中。

在我们的示例中，您将发出的URL请求是`GET /jsonapi/node/article/some-random-uuid?include=field_comments.uid`。

换句话说，你是说“请为`article`的`field_comments`添加资源对象，然后为任意comment的uid字段添加资源对象。

这些“relationship paths(关系路径)”你可以任意书写，没有限制！

响应文档如下所示:
```
{
  "data": {
    "type": "node--article",
    "id": "some-random-uuid",
    "relationships": {
      "field_comments: {
        "data": [{
          "type": "comment",
          "id": "one-random-uuid",
        }, {
          "type": "comment",
          "id": "two-random-uuid",
        }],
        "links": {
          "related": {
            "href": "https://my.example.com/node/article/some-random-uuid/field_comments"
          }
        }
      }
    }
  },
  "included": [{
    "type": "comment",
    "id": "one-random-uuid",
    "relationships": {
      "uid: {
        "data": [{
          "type": "user",
          "id": "another-random-uuid",
        }],
        "links": {
          "related": {
            "href": "https://my.example.com/comment/one-random-uuid/uid"
          }
        }
      }
    }
  }, {
    "type": "comment",
    "id": "another-random-uuid",
    "relationships": {
      "uid: {
        "data": [{
          "type": "user",
          "id": "one-random-uuid",
        }],
        "links": {
          "related": {
            "href": "https://my.example.com/comment/two-random-uuid/uid"
          }
        }
      }
    }
  }, {
    "type": "user",
    "id": "another-random-uuid",
    "attributes": {
      "name": "c0wb0yC0d3r"
    }
  }
}
```

Cool, 我们在一个请求中获得了所有数据！注意用户资源对象如何只包含一次，即使它被引用了两次。

这会降低响应大小。另外，请注意现在每个关系对象中都有一个`data`键。这样，您就可以将包含的资源对象与引用它们的资源对象关联起来。

说到响应大小…在这个例子中，我们通过在一个请求中获取所有资源来节省时间。

但是，在某些情况下，`include`相关的资源对象将使响应时间非常大和/或使获取第一个字节的时间非常缓慢。在这种情况下，并行处理多个请求可能更好。

最后，`include`查询参数在`collection`和`relationship resources`中也被支持，集合中(include)包含可以保存更多请求。