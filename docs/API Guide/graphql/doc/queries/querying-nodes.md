# 查询节点

Drupal中最基本的实体之一是节点实体。
它是任何Drupal站点的构建基础，允许对任何类型的节点实体（bundles）进行查询的灵活系统。
Graphql模块利用了[entityquery]（https://api.drupal.org/api/drupal/core!lib!Drupal.php/function/Drupal%3A%3AentityQuery/8.2.x）的优点,允许非常灵活的查询实体列表。
在Drupal Graphql模块中，如果要对节点实体执行查询，则执行**nodeQuery.**

## 查询Node实体列表
下面是一个查询10条Article节点类型的数据:

```graphql
query {
  nodeQuery(limit: 10, offset: 0, filter: {conditions: [{operator: EQUAL, field: "type", value: ["article"]}]}) {
    entities {
      entityLabel
    }
  }
}
```

让我们更好地分析这个查询。它执行返回10个节点文章类型的数据。
过滤器的语法可能看起来有点复杂(如果您熟悉EntityQuery，可能听起来很熟悉),
但它(EntityQuery)提供了一种可执行任何类型复杂查询的强大的方法。


对上述查询的响应将返回我们在实体EntityLabel中的内容。 

```json
{
  "data": {
    "nodeQuery": {
      "entities": [
        {
          "entityLabel": "10 Reasons why you should be using GraphQL"
        },
        {
          "entityLabel": "Drupal and GraphQL, a love story"
        }
      ]
    }
  }
}
```
正如我们所看到的，上面的结果只是用每个节点label所组成的实体数组。然后让我们看一看关于节点的更复杂的查询。

### 过滤器提示项
Graphql模块允许使用非常复杂的过滤器类型，要深入了解过滤器，请查看本节中的过滤器指南。
## 通过Node id查询单个Node
另一个常见的场景是需要根据其ID获取单个节点。在graphql模块中，可以利用另一个名为**nodeById**

下面是一个返回ID为1的节点的简单示例

```
query {
  nodeById(id: "1") {
    entityLabel
    entityBundle
  }
}
```
简单吧？现在，我们得到的响应是我们在查询字段中要求的，在本例中是EntityLabel和EntityBundle：
```
{
  "data": {
    "nodeById": {
      "entityLabel": "10 Reasons why you should be using GraphQL",
      "entityBundle": "article"
    }
  }
}
```
正如您所看到的，您可以准确地将响应映射到查询中所请求的内容，
从而非常直观地询问新的内容，现在可以看到结果格式中的预期内容。
