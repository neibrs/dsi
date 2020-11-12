# 查询路由

Drupal Graphql模块默认提供的另一个查询类型是**路由**。
查询路由很简单，您提供一个路径作为参数，查询将返回一个URL，其中包含Drupal中每个已定义上下文的字段。
这样就可以为给定路径提取内容语言、当前用户或节点。由contrib模块提供的任何上下文也将被自动获取。

## 执行一个基础路由查询

```graphql
query {
  route(path: "/node/1") {
    alias
  }
}
```

这将返回有关我们刚才提供的路径的信息：

```json
{
  "data": {
    "route": {
      "alias": "/my-node-path-alias"
    }
  }
}
```

## 从路由查询获取上下文信息

正如上面提到的路由查询，您可以做更多的工作，并获取与路由相关联的上下文。
让我们看看如何获取与路由关联的节点或与路由关联的语言的信息。

```graphql
{
  route(path: "/my-node-query-alias") {
    ... on EntityCanonicalUrl {
      nodeContext {
        entityBundle
      }
      languageInterfaceContext {
        id
        name
      }
    }
  }
}
```

返回结果如下 :

```json
{
  "data": {
    "route": {
      "nodeContext": {
        "entityBundle": "article"
      },
      "languageInterfaceContext": {
        "id": "en",
        "name": "English"
      }
    }
  }
}
```
这是一种非常强大的方法，可以通过您正在查询的路由获取相关信息。
访问graphiql并开始尝试路由查询，您可以获得关于别名、语言、用户、节点实体等的信息。
