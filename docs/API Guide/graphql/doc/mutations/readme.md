## Mutations
在graphql中，每当您想要添加、修改或删除存储在服务器上的数据时，都会使用一个突变术语，在本例中是drupal。

不幸的是，由于GRAPHQL的一些技术要求，该模块没有包含一种现成的将常见突变进行peform的方法。
你可以在下面的Amazeelabs博客上阅读更多关于这个的信息。

必须在自定义模块中创建突变(mutations)。在许多情况下，您将扩展现有提供的类来添加、更新或删除实体。
特别是CreatEntityBase、DeleteTentityBase或UpdateTentityBase.

在https://www.amazeelabs.com/en/blog/extending-graphql-part-3-mutations上可以找到实现突变的奇妙资源。 

创建、删除、更新和文件上载的相应示例代码可在此处找到：
https://github.com/drupal-graphql/graphql-示例 

添加文章内容类型（node entity / article bundle）的简单突变(mutations)可能会如下所示：
```graphql
mutation{
  addArticle(input: {title: "Hey"}){
    errors
    violations {
      message
      code
      path
    },
    article: entity{
      ... on NodeArticle {
        nid
      }
    }
  }
}
```

文章中的特定返回字段由您自己决定，并通过突变调用`addArticle(input: {title: "Hey"}) { `后的对象语法指定。
输入参数定义为与内容类型中的字段匹配的相应字段的对象。
“错误”和“冲突”字段是可选的，但有助于确定某个对象是否作为预期目标执行。 

在上面的突变中，我们使用了内联片段`…在nodeArticle nid上返回创建的文章的结果nid。`
我们对返回的实体使用别名'article'，以使结果更加友好。

上述突变(mutations)的结果如下：

```json
{
  "data": {
    "addArticle": {
      "errors": [],
      "violations": [],
      "article": {
        "nid": 15
      }
    }
  }
}
```



其他资源：

* http://graphql.org/learn/queries/#mutations
* https://www.amazeelabs.com/en/blog/extending-graphql-part-3-mutations
