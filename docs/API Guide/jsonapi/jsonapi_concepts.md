JSON API Drupal 核心概念
====================

JSON:API在规范中有许多概念，并不是所有概念都记录在这里。但是，模块的用户不需要完全理解规范的所有概念，就可以使用该模块提高效率。如果您想更深入地了解json:api文档的结构、为什么模块以某种方式执行某些操作，或者只是想了解更多关于模块设计的信息，那么我们鼓励读者阅读json api.org上的规范。

## Document Structure
json:api对json文档的结构以及每个请求和/或响应主体中必须包含哪些信息保持高度一致。

每个请求/响应主体必须位于单个JSON对象的下面
```
{
   // your data here...
}
```
数据或特定于资源的信息必须位于`data`成员下的顶级对象中。“成员”只是JSON对象中的一个预定义键。数据成员可以是对象({})或数组（[]）。创建或更新资源时，这将始终是表示单个项目的单个对象({})。只有在检索多个资源的“集合”时，此属性才会是数组。
```
{
  "data": {
    // Your resource data goes here.
  }
}
```
顶级成员属性包括: `errors`, `meta`, `links`, `included`, `included`属性最常用，将在后面部分阐述。

在`data`和`included`成员下的"resource object"(资源对象)和"resource identifier objects"(资源标识符对象). 资源对象表示所关心的实体。 资源标识符就像数据库的外键，表示不包含任何资源字段的标识符。 Drupal术语来说，一个资源对象JSON数据代表的是一个单独的实体，像Node，Taxonomy或User实体。 资源标识符包含了加载一个实体的必须的信息，比如，只包含类型和ID。

每个资源对象必须包含两个成员，`type`和`id`. 唯一的例外是，在创建新实体时，为了允许Drupal为新资源生成一个ID，可以省略该ID。但是，客户端应用程序在创建新实体时完全可以为资源提供UUID。json:api中的所有id都是uuid。

`type`成员始终是必需的。类型成员的值是由实体类型名称和绑定（如果适用）派生的。实体资源的类型始终遵循模式:entity_type--bundle。例如，核心文章和基本页面节点类型将表示为：node——page和node——article。

因此，在没有必需属性或字段的实体上，可以使用以下JSON创建新实体：
```
{
  "data": {
    "type": "node--my-bundle",
  }
}
```
不过，这并不是很有用。我们需要包括实体的实际值。为此，json:api有两个成员来保存值、属性和关系。属性存储特定于基础资源的值。关系是属于系统中其他资源的值。在Drupal术语中，关系通常表示由实体引用存储的值。在Drupal的核心文章包中，这可能是uid属性。这是因为uid属性是对撰写本文的用户的实体引用。具有属性和关系的文档体可能如下所示：
```
{
  "data": {
    "type": "node--my-bundle",
    "id": "2ee9f0ef-1b25-4bbe-a00f-8649c68b1f7e",
    "attributes": {
      "title": "An Example"
    },
    "relationships": {
      "uid": {
        "data": {
          "type": "user--user",
          "id": "53bb14cc-544a-4cf2-88e8-e9cdd0b6948f"
        }
      }
    }
  }
}
```
如您所见，uid属性位于关系成员之下。与主资源一样，它也包含一个类型和一个ID成员，因为它是一个独立的、不同的资源。

请注意，`uid`没有任何`属性`或`关系`。这是因为json:api将不包含关系的内容，除非使用特殊的查询参数`include`明确要求。稍后在文档中对此进行了详细介绍（请参阅“获取资源（get）”）。

## "Virtual" Resource Identifier(“虚拟”资源标识符)
在某些情况下，Drupal允许与目标资源（指向目标实体的实体引用）建立关系，该资源不存储在数据库中，因此不能通过json:api进行检索。“virtual”资源标识符可能根据其上下文指示不同的情况识别，尽管它始终对应于找不到的资源。
### Drupal核心中“virtual”资源标识符的用法和意义
在Drupal核心中，分类术语`parent`字段是这种特殊情况下最值得注意的例子。这个关系字段可能包含“virtual”分类术语资源的资源标识符。在这种情况下，“virtual”资源标识符标识`<root>`分类术语。因此，这表明引用的术语处于其词汇表的顶层。

假设类似于下面的一个术语集的响应结果如下:

```
{
  "data": {
    "type": "taxonomy_term--tags",
    "id": "2ee9f0ef-1b25-4bbe-a00f-8649c68b1f7e",
    "attributes": {
      "name": "Politics"
    },
    "relationships": {
      "parent": {
        "data": [
          {
            "id": "virtual",
            "type": "taxonomy_term--tags",
            "meta": {
              "links": {
                "help": {
                  "href": "https://www.drupal.org/docs/8/modules/json-api/core-concepts#virtual",
                  "meta": {
                    "about": "Usage and meaning of the 'virtual' resource identifier."
                  }
                }
              }
            }
          }
        ]
      }
    }
  }
}
```
请注意，此术语的父关系（实体引用字段）如何具有资源标识符对象，其中ID不是UUID，而是“虚拟”的。这是必要的，因为顶级或根级术语引用了未存储的<root>术语（target_id=0）作为其父项。

为什么?

考虑到根术语没有存储，并且一个术语可能有多个父项，那么关键问题是：我们如何区分以下术语：
* 只有一个`Term[3]`作为它的父级([3])?
* 都有这个未存储的root `Term`和`Term 3 ([0, 3])的父级？ TODO

答案是，如果json:api省略了未存储的根项0，而不是使用“虚拟”ID，那么就不可能区分这两种情况！

## "Missing" Resource Identifiers(“缺少”资源标识符)
Drupal不会“清除”与已删除资源的关系（引用已删除实体的实体引用字段）。换句话说：Drupal将“悬空”关系（实体引用）保留在适当的位置。

当json:api遇到这种悬空关系时，它将使用“缺少的”资源标识符。

### Drupal核心中“缺少”资源标识符的用法和意义
继续使用为“虚拟”资源标识符提供的示例：分类术语的`parent`字段。假设一个特定的分类术语曾经有“比利时”的分类术语作为它的父项，但是现在“比利时”的分类术语资源已经不存在了——也许是因为比利时这个小国已经不存在了。然后，此关系字段将包含“缺少”分类术语资源的资源标识符。

以假设分类术语的以下响应文档为例：
```
{
  "data": {
    "type": "taxonomy_term--tags",
    "id": "2ee9f0ef-1b25-4bbe-a00f-8649c68b1f7e",
    "attributes": {
      "name": "Politics"
    },
    "relationships": {
      "parent": {
        "data": [
          {
            "id": "missing",
            "type": "unknown",
            "meta": {
              "links": {
                "help": {
                  "href": "https://www.drupal.org/docs/8/modules/json-api/core-concepts#missing",
                  "meta": {
                    "about": "Usage and meaning of the 'missing' resource identifier."
                  }
                }
              }
            }
          }
        ]
      }
    }
  }
}
```
请注意，此术语的父关系（实体引用字段）如何具有资源标识符对象，其中ID不是UUID，而是`missing`。不仅如此，它的类型是`unknown`（因为Drupal不存储被引用实体的bundle，只存储实体类型，因此无法确定json:api资源类型名称）。