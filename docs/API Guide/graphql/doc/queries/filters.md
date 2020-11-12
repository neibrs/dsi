# 过滤器
graphql模块与entityquery集成在一起，以在查询中提供一些非常复杂的过滤。
通常，我们需要做一些复杂的场景，比如将多个过滤器与“and”操作符或“or”操作符组合在一起，
甚至按我们查询的实体中的字段进行过滤，
比如过滤实体引用字段（如分类术语字段）或与另一个节点相关的字段。
所有这些都是可能的，我们将在本文中看到几个这样的例子。 
## 过滤器语法

在Drupal的GraphQL语法分为3个部分:

### conditions
条件是一个过滤器列表，它以某种方式对给定字段上的给定值筛选查询。让我们来看一个例子
```graphql
query {
  nodeQuery(filter: {conditions: [{operator: EQUAL, field: "type", value: ["article"]}]}) {
    entities {
      entityLabel
    }
  }
}
```
我们在这里过滤文章列表。我们过滤Node的**type**，使其**EQUAL**等于“article”。您可以提供以下运行符中的一个：
- EQUAL
- NOT_EQUAL
- SMALLER_THAN
- SMALLER_THAN_OR_EQUAL
- GREATER_THAN
- GREATER_THAN_OR_EQUAL
- IN
- NOT_IN
- LIKE
- NOT_LIKE
- BETWEEN
- NOT_BETWEEN
- IS_NULL
- IS_NOT_NULL
正如你所看到的，这里的操作符非常丰富。
我们可以在这里提供多个条件，并且不指定组或连词，它们都将用作“AND”组合，这意味着为了传递查询，所有条件都必须匹配。
让我们看看如何通过分组和连词从中获得更多。
### (连接)conjunctions

我们想要提供多个过滤器，使用“and”操作符集成它们，但如果我们想要“or”组合呢？这就是连接键的作用，它可以有两个值 
- AND
- OR

当提供条件时，将使用此运算符组合。在下面的示例中，我们调整了前面的查询，以返回类型为“文章”或“客户”的所有实体。

```
query {
  nodeQuery(filter: {conjunction:OR, conditions: [{ operator:EQUAL, field:"type", value:["article"] },{ operator:EQUAL, field:"type", value:["client"] }] }) {
    entities {
      entityLabel
    }
  }
}
```

### (组)groups
在一个更加复杂的场景中，我们实际上可能将这两种情况结合在一起，
我们希望查询的某些部分条件应该使用“或”运算符以及“和”运算符。
对于这些情况，我们可以利用组(group)。

组允许我们将查询分解为不同的部分，以便像在普通数据库中那样实现更复杂的查询。
当提供一个组时，我们还可以提供一个连接，以便使用“或”或“和”运算符返回组内的条件。
我们也可以在组内有组，这样我们可以在这里真正深入到我们想要的程度。 

让我们来看一个例子，我们希望像获取所有类型为“文章”**或**“客户”**，两者的状态都是**已发布**。

```
query {
  nodeQuery(filter: {conjunction: AND, 
    groups: [
      {conjunction: OR, conditions: [{operator: EQUAL, field: "type", value: ["article"]}, {operator: EQUAL, field: "type", value: ["client"]}]},
      {conditions: [{operator: EQUAL, field: "status", value: ["1"]}]}
    ]}) {
    entities {
      entityLabel
    }
  }
}
```



就这样！我们可以使用这种语法进行超复杂的查询。请记住，这将被转换为EntityQuery，因此它很容易将其与查询的结果联系起来。 

## 筛选实体中的字段

一个非常常见的场景是要过滤给定字段的值，我们之前看到了如何过滤实体的类型。
让我们看看如何筛选实体中字段的值。让我们调整第一个示例来过滤状态值，而不是类型值。
```
query {
  nodeQuery(filter: {conditions: [{operator: EQUAL, field: "status", value: ["1"]}]}) {
    entities {
      entityLabel
    }
  }
}
```

这里我们得到所有被发布的实体。

## 筛选实体中的自定义字段

如果我们想筛选一个只存在于该实体中的字段，它就没有太大的不同。
让我们来看一个简单的例子。假设我们有一个实体“客户”，它有一个自定义电话号码字段:

```
query {
  nodeQuery(filter: {conditions: [
    {operator: EQUAL, field: "type", value: ["client"]},
    {operator: EQUAL, field: "telephone", value: ["918273736"]}
  ]}) {
    entities {
      entityLabel
    }
  }
}
```

我们可以通过提供字段名和值以及所需的运算符轻松地按此字段进行筛选。
## 筛选实体引用字段 

还有一个常见的场景是我们需要过滤字段，但是这个字段是一个实体引用，
这意味着我们应该提供要在Value属性中引用的实体的键。
在这个例子中，假设我们在“文章”实体中有一个实体引用到“客户”。此字段是节点类型的实体引用。我们筛选此字段的方法如下:

```
query {
  nodeQuery(filter: {conditions: [
    {operator: EQUAL, field: "type", value: ["article"]},
    {operator: EQUAL, field: "client.entity.nid", value: ["13"]}
  ]}) {
    entities {
      entityLabel
    }
  }
}
```
如果我们过滤的实体是“term reference”类型，那么`client.entity.nid`应该变成`client.entity.tid`，因为它现在应该引用一个term id而不是节点id。
