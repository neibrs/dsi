# 查询变量
到目前为止，我们已经研究过查询，但它们都是静态的。
我们一直在将查询中的值直接传递给我们想要获取的任何内容。那么，我们如何做相同的事情，但以动态的方式提供这些变量值呢？

官方[GraphQL documentation](http://graphql.org/learn/queries/#variables)有关于变量的大量的示例

## 怎样构建一个可接收变量的查询呢

使查询准备好使用变量的方法是将变量作为查询的参数提供，然后在内部使用。
让我们以“查询节点”示例为例，
在该示例中，我们希望查询“文章”类型的节点，并使该类型成为动态的，这样我们就可以传递我们想要传递的任何内容。
旧的查询看起来像这样 :

```graphql
query {
  nodeQuery(limit: 10, offset: 0, filter: {conditions: [{operator: EQUAL, field: "type", value: ["article"]}]}) {
    entities {
      entityLabel
    }
  }
}
```

我们现在可以把它重构成这样:

```graphql
query getNodeType($type:String!, $limit:Int!, $offset:Int!) {
  nodeQuery(limit: $limit, offset: $offset, filter: {conditions: [{operator: EQUAL, field: "type", value: [$type]}]}) {
    entities {
      entityLabel
    }
  }
}
```
现在，我们可以使用相同的查询来检索“文章”、“客户”或任何其他我们想要的节点**类型**。
我们还为**limit**和**offset**提供变量，
因为graphql是类型化的，所以我们必须为它提供这些变量应该是的类型。

## 在GraphiQL中测试
所以，通过导航到**graphql/explorer**来访问graphql，并尝试上面的查询，
您会发现在左下方有一个**变量**框，单击它，它将弹出并填充变量，如下所示：

```json
{
    "type": "article",
    "limit": 10,
    "offset": 0
}
```
