## Metatag

使用metatag模块和graphql最简单的方法是使用[graphql metatag]（https://www.drupal.org/project/graphql_metatag）模块。
它内置了对使用metatags和graphql的支持.

## Metatag Queries

```
{
  nodeById(id: "1") {
    entityId
    ... on NodeArticle {
      entityId
      entityMetatags {
        key
        value
      }
    }
  }
}
```

响应信息如下 : 

```
{
  "data": {
    "nodeById": {
      "entityId": "1",
      "entityMetatags": [
        {
          "key": "title",
          "value": "Article name | My drupal site"
        },
        {
          "key": "canonical",
          "value": "https://websiteurl.com/node/1"
        }
      ],
    }
  }
}
```

通过这种方式，您可以很容易地在Drupal中的节点中操作SEO信息，但是仍然能够通过从节点中提取这些信息，
将其正确地注入到应用程序中。

当然，您可以在任何类型的查询中使用它，这些查询将返回包含元标记信息的实体。
作为查询路由并从该实体获取的示例，metatag信息如下所示：

``` 
{
  route(path: "/article-name") {
    ... on EntityCanonicalUrl {
      entity {
        entityLabel
        entityMetatags {
          key
          value
        }
      }
    }
  }
}

```

这将返回关于这个特定路由的任何信息，包括请求的元信息。

```
{
  "data": {
    "route": {
      "entity": {
        "entityLabel": "Article name",
        "entityMetatags": [
          {
            "key": "title",
            "value": "Article name | My drupal site"
          },
          {
            "key": "canonical",
            "value": "https://websiteurl.com/article-name"
          }
        ]
      }
    }
  }
}
```
参见: [routes documentation](queries/routes.md).

### 已知问题 

当前有一个[Issue]（https://github.com/drupal-graphql/graphql/issues/609）关于与graphql模块一起使用metatag模块：

* "如果模块（例如metatag）引入了新的基元数据类型，那么它不是派生类型的一部分，但是使用它的任何字段都将引用它。
这将导致“缺少类型metatag.”的异常。“*

因此，现在您需要包含一个自定义标量作为解决方法，以避免由于缺少类型而导致的graphql错误。
在您自己的自定义模块内创建一个文件，命名为“MetatagScalar.php”，其中将定义自定义标量。
在这个例子中，模块的名称是graphql_custom，从下面的名称空间中可以看到。
在定义命名空间时，请确保不要与现有命名空间冲突。 

```
<?php

namespace Drupal\graphql_custom\Plugin\GraphQL\Scalars;

use Drupal\graphql\Plugin\GraphQL\Scalars\Internal\StringScalar;

/**
 * Metatag module dummy type.
 *
 * Metatag module defines a custom data type that essentially is a string, but
 * not called string. And the GraphQL type system chokes on that.
 *
 * @GraphQLScalar(
 *   id = "metatag",
 *   name = "metatag",
 *   type = "string"
 * )
 */
class MetatagScalar extends StringScalar {

}
```
