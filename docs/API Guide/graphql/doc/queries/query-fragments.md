# 代码片断

顾名思义，graphql片段是一个查询片段。它们主要有两个用途:

* **Executing part of a query conditionally** 仅当结果为指定类型时。在上面的示例中，只有当ID为1的节点是一篇文章时，才会计算fieldSubtitle。如果结果是一个基本页，那么片段将被省略，响应将只缩短一个字段，而不会引发任何异常。
* **Reusability**. 一个片段可以被命名并被多次使用。

让我们看看下面的查询：

```graphql
{
  nodeById(id: "1", language: en) {
    ... on NodeArticle {
      fieldCategory {
        entity {
          ...termFragment
        }
      }
      fieldTags {
        entity {
          ...termFragment
        }
      }
    }
  }
}

fragment termFragment on TaxonomyTerm {
  name
  tid
}
```

此查询中有两个片段。从第3行开始的第一个片段是一个**内联片段**。
我们需要它，因为fieldCategory和fieldTags只附加到文章，nodeById可以返回任何节点。
第18行定义的另一个是一个名为fragment，因为我们不需要为fieldCategory和fieldTags重复定义子查询。


您可以利用片段使非常复杂的查询书写得更容易理解，
方法是将它们分解成更小的片段，它们也是一种很好的方式来共享常见的东西，
如上面的termFragment，使代码更干净，更容易重构。 