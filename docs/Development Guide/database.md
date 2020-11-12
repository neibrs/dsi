Database abstraction layer(数据库抽象层)
======================================

用同样的代码访问不同的数据库服务器。

## 概述

平台提供了统一的查询API，来查询不同的底层数据库。
平台的查询API建立在PHP的PDO之上，并继承了大部分语法和语义。
除此之外，查询API还提供结构化的方法来构造复杂的查询，并采用良好的安全实践来保护数据库。

参考[Drupal文档](https://www.drupal.org/docs/8/api/database-api/database-api-overview)。

## Enities(实体)查询

对实体或字段的查询应该使用实体查询。参阅[entity(实体)](entity_api.md)。

## <a name="sim_query">简单的SELECT查询</a>

简单的SELECT查询指不涉及实体的查询;数据库抽象层提供了`\Drupal::database()->query()`和`\Drupal::database()->queryRange()`两个方法来执行SELECT操作；
这两个方法返回的结果集可以使用foreach循环来迭代(该结果集是`\Drupal\Core\Database\StatementInterface`接口类型的数组);

简单数据库查询动作可以在任意数据库引擎中进行查询操作，简单数据库查询不包括动态查询(占位符查询除外).
如果需要复杂的数据查询或是在一些数据库中查询操作存在差异时，可以使用[动态查询](#sec_dynamic)方法.

提醒: 这里使用的`\Drupal::database()`获取数据库对象的方式是个全局方法，在大多数的类中，
使用依赖注入或使用服务的方式来获取数据库连接对象进行后续的数据库查询。

要使用简单的数据库查询函数，需要对原始的SQL查询语句进行一些修改:
* 将表名放在{}中，Drupal允许使用数据库表名前缀，因此您不能确定表的实际名称,Drupal将计算出正确的名称.
* 不要将条件的值直接放入查询语句中，要使用占位符；<code>:</code>冒号开头，连接相应的参数或字段名表示一个字段查询值；这样可以防止数据库注入攻击。
* `LIMIT`不同于通用的数据库查询语法，使用`\Drupal::database()->queryRange()`代替`\Drupal::database()->query()`;

例如，需要运行如下查询时:

```
SELECT e.id, e.title, e.created FROM example e WHERE e.uid = $uid
  ORDER BY e.created DESC LIMIT 0, 10;
```
Drupal的数据库查询代码如下:

```
$result = \Drupal::database()
  ->queryRange('SELECT e.id, e.title, e.created
  FROM {example} e
  WHERE e.uid = :uid
  ORDER BY e.created DESC', 0, 10, array(
  ':uid' => $uid,
));
foreach ($result as $record) {

  // Perform operations on $record->title, etc. here.
}
```

如果需要查询条件，可以这样: `WHERE e.my_field = 'foo'`

如果需要占位符查询，可以这样:

```
WHERE e.my_field = :my_field
                  ... array(':my_field' => 'foo') ...`
```


## <a name="sec_dynamic">动态Select查询</a>

动态查询是在简单查询不能完成相应的操作时考虑的方式，但如果要进行的查询操作涉及实体或字段，建议使用**Entity Query API**查询，更多信息请参看[实体查询API](entity_api.md)

下面是[简单数据库查询](#sim_query)操作的示例:
```
$result = \Drupal::database()
  ->select('example', 'e')
  ->fields('e', array(
  'id',
  'title',
  'created',
))
  ->condition('e.uid', $uid)
  ->orderBy('e.created', 'DESC')
  ->range(0, 10)
  ->execute();
```
这种方式也可以添加字段别名，或连接其他数据库表进行查询的方法，更多信息可以参看[Drupal开发文档](https://www.drupal.org/developing/api/database).

上面的数据库动态查询大多支持链式操作，其中有一部分是不支持这种链式操作的，比如:
* `join(), innerJoin(), etc`: 这些方法返回已连接表的别名。
* `addField()`, 这个方法返回字段别名。

## INSERT, UPDATE, 和 DELETE 查询

插入、更新和删除查询需要特别注意，以便在数据库之间保持一致；不能直接使用`\Drupal::database()->query()`执行插入、更新、删除操作；
而应该使用` \Drupal::database()->insert(), \Drupal::database()->update(), and \Drupal::database()->delete() `这样的方式来操作。如果有其他条件查询，可以参看上面的[数据库简单查询](#sim_query)
例如，如果需要执行这样的查询: `INSERT INTO example (id, uid, path, name) VALUES (1, 2, 'path', 'Name');`
Drupal的查询代码应该如下:
```
$fields = array(
  'id' => 1,
  'uid' => 2,
  'path' => 'path',
  'name' => 'Name',
);
\Drupal::database()
  ->insert('example')
  ->fields($fields)
  ->execute();
```

## 事务

Drupal支持事务，包括对不支持事务的数据库的透明回退。要启动新事务，请调用StartTransaction(),如下所示：
```
$transaction = \Drupal::database()
  ->startTransaction();
```
只要变量$transaction保持在作用域内，事务将保持打开状态；当$transaction被销毁时，事务将被提交。
如果您的事务嵌套在另一个事务中，那么Drupal将跟踪每个事务，并且只在最后一个事务对象超出范围时提交最外部的事务（当所有相关查询成功完成时）。

```
function my_transaction_function() {
  $connection = \Drupal::database();

  // The transaction opens here.
  $transaction = $connection
    ->startTransaction();
  try {
    $id = $connection
      ->insert('example')
      ->fields(array(
      'field1' => 'mystring',
      'field2' => 5,
    ))
      ->execute();
    my_other_function($id);
    return $id;
  } catch (Exception $e) {

    // Something went wrong somewhere, so roll back now.
    $transaction
      ->rollBack();

    // Log the exception to watchdog.
    watchdog_exception('type', $e);
  }

  // $transaction goes out of scope here.  Unless the transaction was rolled
  // back, it gets automatically committed here.
}
function my_other_function($id) {
  $connection = \Drupal::database();

  // The transaction is still open here.
  if ($id % 2 == 0) {
    $connection
      ->update('example')
      ->condition('id', $id)
      ->fields(array(
      'field2' => 10,
    ))
      ->execute();
  }
}
```

### 数据库连接对象
之前的示例使用了代码中经常使用的`\Drupal::database()->select()`和`\Drupal::database()->query()`函数。
在某些类中，可能已经有一个数据库连接对象的成员变量，
或者可以通过依赖项注入将其传递给类构造函数。如果是这种情况，您可以查看`\Drupal::database()->select()`和其他函数的代码，
以了解如何从连接变量中获取查询对象。例如：
```
$query = $connection
  ->select('example', 'e');
```
如果已经有$connection的数据库连接对象，上例等同于：
```
$query = \Drupal::database()
  ->select('example', 'e');
```
参考[服务与依赖注入容器](container.md)

##参考
[Drupal API](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Database%21database.api.php/group/database)
