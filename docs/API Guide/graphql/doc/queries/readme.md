# 查询 

graphql是一种查询语言，所以我们首先要讨论的是如何开始对drupal进行查询。
graphql的一大好处是查询语法和相应的响应看起来是非常直观。
本质上，查询与您希望响应看起来非常相似，但没有值。让我们看看我们在介绍中看到的示例

```graphql
query {
  user: currentUserContext{
    ...on User {
      name
    }
  }
}
```
启用模块后，您可以通过GET请求在浏览器中运行上述查询。注意，如果您已经登录，查询应该返回一个结果。
如果希望匿名用户运行以下查询，则需要启用“Execute arbitrary GraphQL requests”权限。
您还可以在随模块提供的graphiql浏览器中运行此查询，网址为：`/graphql/explorer`。

`[你的域]/graphql？query=query user:currentUserContext…on%20user name`


这将返回类似于以下内容的结果： 

```json
{
  "data": {
    "user": {
      "name": "admin"
    }
  }
}
```

正如我们所看到的，我们返回的数据与我们请求的字段完全匹配，
在本例中是用户的名称。在本节中，我们将分析如何查询Drupal中的任何实体。
我们将再次使用Graphql资源管理器，您可以通过访问Drupal站点上的`/graphql/explorer`来测试这里的任何示例。

在本节中，我们将了解如何查询Drupal后端的多个部分，以及如何对节点、分类法、路由等执行查询。 

## 命名

Graphql命名约定与Drupal的稍有不同。 

* 字段和属性是驼峰法。这意味着drupal中的**field_image**而在graphql中变为**fieldImage**,
    而**revision_log**属性变为**revisionLog**。

* 实体类型和包使用第一个字母大写的驼峰法**taxonomy_term**变为**taxonomyTerm**，
标签词汇变为**taxonomyTermTags**。正如我们看到的，捆绑包的前缀是实体类型名称。 
因此有时您所只需要输入“cmd+space”就查看哪些字段可使用。

## 在查询中的字段

Graphql在不需要额外请求的情况下可以从非常不同的地方获取字段，这是使用这种查询语言的好处之一。让我们看看这个例子：

```graphql
query {
  nodeById(id: "1", language: en) {
    entityId
    entityCreated

    title
    status

    ... on NodeArticle {
      fieldSubtitle
    }
  }
}
```

上面的查询从3个不同的地方获取信息：

* **entityId**和**entityCreated**来自于实体接口.所有实体对象都可使用这些字段.
nodeById查询将返回一个实现了Entity Interface接口的Node Interface接口类型数据
* 定义在Node Interface接口中的title和status可被所有Node使用,不仅是内容类型.
* **fieldSubtitle** 是Article内容类型中的字段(Drupal中**field_subtitle** 字段) 它不是Node，
也不是Entity Interface接口字段数据, 它仅对NodeArticle内容类型有用。
**nodebyId**可惜返回任意node,不仅仅是Article, 
因此需要在[GraphQL Fragment](http://graphql.org/learn/queries/#fragments)包装fieldSubtitle.

如果把上面的代码粘贴到GraphQL中，将得到以下代码:

```json
{
  "data": {
    "nodeById": {
      "entityId": "1",
      "entityCreated": "2017-12-01T00:00:00+0100",
      "title": "GraphQL rocks",
      "status": 1,
      "fieldSubtitle": "Try it out!"
    }
  }
}
```



