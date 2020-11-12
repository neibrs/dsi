Filtering for JSON:API
======================

集合是资源列表。在分离的站点中，它们是用来创建“新内容”列表或客户机端的“我的内容”部分等内容的工具。

但是，当您向集合端点（如/jsonapi/node/article）发出未过滤的请求时，您只需获得允许查看的每一篇文章。

如果没有过滤器，你就不能只得到你的文章或者关于骆驼的文章,即不能过滤得到你希望得到的数据 。

本指南将教您如何像专业人员一样构建过滤器。

**内容**
* [摘要](#summary)
* [构建过滤器](#build_filter)
* [条件组](#condition_group)
* [路径](#path)
* [快捷键](#shortcuts)
* [过滤器和访问控制](#filter_access_control)
* [过滤器示例](#filter_example)

## 快速示例
最简单，最常用的方式是键值对过滤:
`?filter[field_name]=value&filter[field_other]=value`

## <a name="summary">摘要</a>
JSON:API模块有一些最健壮和功能丰富的过滤特性。不过，所有这些能力都有一点学习曲线。
在本文的最后，您将能够应对您所可能面临的问题构建复杂的查询进行过滤，例如“如何获取关于美洲驼或世界上最快的动物王国成员游隼的文章列表的作者？

我们将从最基本的方面着手，在那之后，我们将向您展示一些快捷方式，这些快捷方式可以使编写过滤器更快、更精简。最后，我们将看到从现实世界中提取的一些过滤器示例。

如果您不是Drupal的新手，那么您可能已经在这些方面使用了视图(view)模块。与Drupal核心附带的REST模块不同，json:api不能导出视图(views)结果。

集合是json:apis api第一个替换视图中导出的“rest-displays”。(Collections are JSON:APIs API-First replacement for exported "REST displays" in Views.)

## <a name="build_filter">构建过滤器</a>
json:api过滤器的基本构建块是条件和组。条件断言某些东西是真的，组允许您将这些断言组合成逻辑集，以生成更大的条件组。这些集合可以嵌套以进行非常精细的查询。您可以将这些嵌套集想象成树：
```
Conventional representation:

a( b() && c( d() || e() ) )

Tree representation:

   a
  / \
 b & c
    / \
   d | e

In both representations:

"d" and "e" are members of "c" in an OR group.
"b" and "c" are members of "a" in an AND group.
```
那么，在一个条件里面是什么？
记住，一个条件告诉你关于一个资源的一个TRUE或者FALSE的内容，以及你对它所做的一些断言，比如“这个实体是由一个特定的用户创建的吗？”当某个资源的条件为false时，该资源将不会包含在集合中。

一个条件包含3个主要部分: `path`, `operator`, `value`; 可理解为`filter[field_other]=value`,
* A 'path' identifies a field on a resource `filter[field_other]`
* An 'operator' is a method of comparison `=`
* A 'value' is the thing you compare against `value`

形如: ($field !== 'space')

如果想要过滤用户的第一个名，条件可以像这样:
```
?filter[a-label][condition][path]=field_first_name
&filter[a-label][condition][operator]=%3D  <- encoded "=" symbol
&filter[a-label][condition][value]=Janis
```

如果系统中，有很多第一个名为Janis呢？添加最后一个名以"J"开头的条件查询:
```
?filter[first-name-filter][condition][path]=field_first_name
&filter[first-name-filter][condition][operator]=%3D  <- encoded "="
&filter[first-name-filter][condition][value]=Janis

&filter[last-name-filter][condition][path]=field_last_name
&filter[last-name-filter][condition][operator]=STARTS_WITH
&filter[last-name-filter][condition][value]=J
```

过滤器使用的操作符号包括但不限于`=`,`START_WITH`, 完整的操作符号定义如下:
```
\Drupal\jsonapi\Query\EntityCondition::$allowedOperators = [
  '=', '<>',
  '>', '>=', '<', '<=',
  'STARTS_WITH', 'CONTAINS', 'ENDS_WITH',
  'IN', 'NOT IN',
  'BETWEEN', 'NOT BETWEEN',
  'IS NULL', 'IS NOT NULL',
];
```

## <a name="condition_group">条件组</a>
现在我们知道如何构建条件，但是我们还不知道如何构建条件组。我们怎样才能像上面看到的那样建造一棵树？

为了做到这一点，我们需要有一个“group”。一个组是由一个“连接词”连接的一组条件。所有组都有连词，连词可以是“AND”或“OR”。

现在我们的过滤器有点更具体了！假设我们想找到所有姓氏以“J”开头，名字为“Janis”或“Joan”的用户。
为此，我们添加了一个组：
```
?filter[rock-group][group][conjunction]=OR
```
然后，我们需要将过滤器分配给新组。
为此，我们添加一个`memberOf`的键。每个条件和条件组都可有一个`memberOf`的键.
注意：假设没有memberOf键,每个筛选器都是“root”组的一部分，并且有和的连接。

现在如下所示:
```
?filter[rock-group][group][conjunction]=OR

&filter[janis-filter][condition][path]=field_first_name
&filter[janis-filter][condition][operator]=%3D
&filter[janis-filter][condition][value]=Janis
&filter[janis-filter][condition][memberOf]=rock-group

&filter[joan-filter][condition][path]=field_first_name
&filter[joan-filter][condition][operator]=%3D
&filter[joan-filter][condition][value]=Joan
&filter[joan-filter][condition][memberOf]=rock-group

&filter[last-name-filter][condition][path]=field_last_name
&filter[last-name-filter][condition][operator]=STARTS_WITH
&filter[last-name-filter][condition][value]=J
```

这样看起来就像下面的树一样:
```
   a   a = root-and-group
  / \
 /   \    b = last-name-filter
b     c   c = rock-group
     / \
    /   \    d = janis-filter
   d     e   e = joan-filter

```
这样就可以按个人意愿组装更深层次的树形条件组进行过滤了。

## <a name="path">Paths(路径)</a>
条件还有最后一个特性：“路径”
路径提供了一种基于关系值进行过滤的方法。

到目前为止，我们只是根据用户资源上的假设字段“名字”和“姓氏”进行筛选。

假设我们希望根据用户职业的名称进行筛选，职业类型存储在一个单独的资源中。我们可以添加这样的过滤器：
```
?filter[career][condition][path]=field_career.name
&filter[career][condition][operator]=%3D
&filter[career][condition][value]=Rockstar
```
路径使用“点符号”来遍历关系。

如果资源具有关系，则可以通过将关系字段名和关系的字段名之间加.来连接进行添加筛选器。

您甚至可以通过添加更多字段名和点来添加过滤关系。

提示：可以通过在路径中放置非负整数来筛选关系的特定索引。因此路径some_relationship.1.some_attribute将只按第二个相关资源筛选。

提示：您可以按字段的子属性进行筛选。例如，即使field_phone不是一个关系，类似field_phone.country_code的路径也可以工作。


## <a nname="shortcuts">快捷方式</a>
要输入的字符太多了。大多数时候，您不需要这么复杂的过滤器，对于这些情况，json:api模块有一些“快捷方式”来帮助您更快地编写过滤器。

当运算符为=时，不必包括它。只是假设而已。因此：
```
?filter[a-label][condition][path]=field_first_name
&filter[a-label][condition][operator]=%3D  <- encoded "=" symbol
&filter[a-label][condition][value]=Janis

becomes

?filter[janis-filter][condition][path]=field_first_name
&filter[janis-filter][condition][value]=Janis
```
同样很少需要用同一字段过滤两次（尽管这是可能的）。所以，当操作符为=时，不需要按同一个字段过滤两次，路径可以是标识符。因此：
```
?filter[janis-filter][condition][path]=field_first_name
&filter[janis-filter][condition][value]=Janis

becomes

?filter[field_first_name][value]=Janis
```
这个额外的value令人讨厌。这就是为什么您可以将最简单的相等性检查减少到键值形式的原因：
```
?filter[field_first_name]=Janie
```

## <a name="filter_access_control">过滤和访问控制</a>
首先，警告：不要犯混淆访问控制和过滤器的概念。只是因为您编写了一个过滤器来删除用户不想看到的内容，并不意味着它是不可访问的。始终在后端执行访问检查。

有了这个附加说明，我们来谈谈使用过滤器来完成访问控制。为了提高性能，您应该过滤掉用户看不到的内容。json:api问题队列中最常见的请求就可以通过这个简单的技巧来解决！

如果用户不能查看未发布的内容，那么过滤器如下:
`?filter[status][value]=1`
使用此方法，您将减少需要发出的不必要请求的数量。这是因为json:api不会为无权访问资源的用户返回数据。通过检查json:api文档的meta.errors部分，可以看到哪些资源可能受到了影响。

所以，尽可能提前过滤掉不可访问的资源。

## <a name="filter_example">过滤示例</a>
1. **仅获取已发布的nodes**.
一个非常常见的场景是只获取已发布的节点。这是一个非常容易添加的过滤器。
```
SHORT
filter[status][value]=1

NORMAL
filter[status-filter][condition][path]=status
filter[status-filter][condition][value]=1
```

2. **通过实体引用的获取Nodes
一种常见的策略是通过实体引用过滤内容。
```
SHORT
filter[uid.uuid][value]=BB09E2CD-9487-44BC-B219-3DC03D6820CD

NORMAL
filter[author-filter][condition][path]=uid.uuid
filter[author-filter][condition][value]=BB09E2CD-9487-44BC-B219-3DC03D6820CD
```

3. **嵌套过滤器：获取用户管理员创建的节点**
可以从被引用实体（如用户、分类字段或任何实体引用字段）中筛选字段。您可以很容易地做到这一点，但只需使用以下符号。reference_field.nested_field 本例中，引用字段是用户的uid，name是用户实体的字段。
```
SHORT
filter[uid.name][value]=admin

NORMAL
filter[name-filter][condition][path]=uid.name
filter[name-filter][condition][value]=admin
```

4. **使用数组过滤：获取用户创建的节点[admin,join]**
您可以为一个过滤器提供多个值以供其搜索。在字段和值键中间，可以向条件添加一个运算符。通常它是“=”，但您也可以使用“in”，“not in”，“>”，“<”，“<>”，between“。

对于这个例子，我们将使用in操作符。注意，我在值后面添加了两个方括号，使其成为一个数组。
```
NORMAL
filter[name-filter][condition][path]=uid.name
filter[name-filter][condition][operator]=IN
filter[name-filter][condition][value][]=admin
filter[name-filter][condition][value][]=john
```

5. **分组筛选器：获取由管理员发布和创建的节点**
现在，让我们结合上面的一些例子，创建下面的场景。
其中user.name=admin和node.status=1；
```
filter[and-group][group][conjunction]=AND
filter[name-filter][condition][path]=uid.name
filter[name-filter][condition][value]=admin
filter[name-filter][condition][memberOf]=and-group
filter[status-filter][condition][path]=status
filter[status-filter][condition][value]=1
filter[status-filter][condition][memberOf]=and-group
```
您不必添加and-group，但我发现这通常会容易一些。

6. **分组筛选器：获取由管理员提升或粘性并创建的节点**
如分组部分所述，您可以将组放入其他组中。
`WHERE (user.name = admin) AND (node.sticky = 1 OR node.promoted = 1)`
```
# Create an AND and an OR GROUP
filter[and-group][group][conjunction]=AND
filter[or-group][group][conjunction]=OR

# Put the OR group into the AND GROUP
filter[or-group][group][memberOf]=and-group

# Create the admin filter and put it in the AND GROUP
filter[admin-filter][condition][path]=uid.name
filter[admin-filter][condition][value]=admin
filter[admin-filter][condition][memberOf]=and-group

# Create the sticky filter and put it in the OR GROUP
filter[sticky-filter][condition][path]=sticky
filter[sticky-filter][condition][value]=1
filter[sticky-filter][condition][memberOf]=or-group

# Create the promoted filter and put it in the OR GROUP
filter[promote-filter][condition][path]=promote
filter[promote-filter][condition][value]=1
filter[promote-filter][condition][memberOf]=or-group
```

7. **使用'title'包含'foo'的条件来过滤nodes**
```
SHORT
filter[title][operator]=CONTAINS&filter[title][value]=Foo

NORMAL
filter[title-filter][condition][path]=title
filter[title-filter][condition][operator]=CONTAINS
filter[title-filter][condition][value]=Foo
```

8. **按非标准复杂字段筛选(如:地址字段)**
```
FILTER BY LOCALITY
filter[field_address][condition][path]=field_address.locality
filter[field_address][condition][value]=Mordor

FILTER BY ADDRESS LINE
filter[address][condition][path]=field_address.address_line1
filter[address][condition][value]=Rings Street
```