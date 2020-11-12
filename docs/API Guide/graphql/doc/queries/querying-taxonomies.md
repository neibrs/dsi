# 查询术语
Drupal中的另一个常见实体是(Taxonomy)分类法，它为Drupal的使用提供了巨大的用途。
Taxonomy也可以使用Graphql进行查询，其结构与查询节点的方式非常相似。
对于Taxonomy，查询**taxonomyTermQuery**和**taxonomyTermByID**可用于查询分类术语列表或单个分类术语。 

## 从一个词汇表内查询所有术语
分类的一个常见用例是获取给定词汇表的术语列表。
因为我们再次使用EntityQuery，所以该格式看起来非常类似于在前一篇文章中查询节点。
下面是对带有**vid**“tags”的词汇表中的术语的查询如下所示： 

```graphql
query {
  taxonomyTermQuery(limit: 10, offset: 0, filter: {conditions: [{operator: EQUAL, field: "vid", value: ["tags"]}]}) {
    entities {
      entityLabel
    }
  }
}
```
这将获取最多10个属于词汇“Tags”的术语。结果将再次非常像我们要求的：
```json
{
  "data": {
    "taxonomyTermQuery": {
      "entities": [
        {
          "entityLabel": "Drupal"
        },
        {
          "entityLabel": "GraphQL"
        },
        {
          "entityLabel": "Web"
        },
        {
          "entityLabel": "React"
        }
      ]
    }
  }
}
```

## 通过一个术语ID查询
查询单个术语可以利用查询**taxonomyTermById**来完成，该查询的参数是我们要获取的术语的ID。
```graphql
query {
  taxonomyTermById(id: "3") {
    entityLabel
  }
}
```
这个查询的结果将是我们要求的内容，在本例中，仅EntityLabel：

```json
{
  "data": {
    "taxonomyTermById": {
      "entityLabel": "Drupal"
    }
  }
}
```

再一次说明，您可以在这个查询中查询多个项目，所以请确保尝试graphiql并探索可以提取哪些项。
