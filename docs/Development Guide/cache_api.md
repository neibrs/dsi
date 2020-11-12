Cache API 
========

## Basics

注意: 本文所使用的方法，如果没有指定，所有的方法都属于`\Drupal\Core\Cache\CacheBackendInterface`

缓存API用于存储计算时间较长的数据。缓存可以是永久的，也可以仅在特定时间段内有效，并且缓存可以包含任何类型的数据。

要使用缓存API，请执行以下操作：
* 通过\Drupal::cache（）或通过注入缓存服务请求缓存对象。
* 为数据定义缓存ID（cid）值。cid是一个字符串，它必须包含足够的信息才能唯一地标识数据。
例如，如果数据包含已翻译的字符串，则cid值必须包括为页面选择的接口文本语言。
* 调用get()方法尝试缓存读取，以查看缓存是否已包含您的数据。
* 如果数据不在缓存中，请计算它并使用set()方法将其添加到缓存中。set()的第三个参数可用于控制缓存项的生存期。

例:
```$xslt
$cid = 'mymodule_example:' . \Drupal::languageManager()
  ->getCurrentLanguage()
  ->getId();
$data = NULL;
if ($cache = \Drupal::cache()
  ->get($cid)) {
  $data = $cache->data;
}
else {
  $data = my_module_complicated_calculation();
  \Drupal::cache()
    ->set($cid, $data);
}
```

注意上面例子中$data和$cache->data的用法。
调用\Drupal::cache()->get()返回一条记录，该记录包含数据属性中\Drupal::cache()->set()存储的信息以及有关缓存数据的其他元信息。
为了利用缓存的数据，您可以通过$cache->data访问它。

## Cache bins(缓存箱)
缓存存储被分为“箱”，每个箱包含各种缓存项。每个缓存箱可以单独配置；请参见[配置](config_api.md)。

请求缓存对象时，可以在对\Drupal::cache()的调用指定bin名称。
或者，您可以通过从容器中获取服务“cache.nameofbin”来请求一个bin。默认的bin称为“default”，服务名为“cache.default”，用于存储通用和常用的缓存。

其他通用的缓存箱(cache bin)如下:
* **bootstrap** 大多数请求从开始到结束都需要的数据，这些数据对变化有非常严格的限制，很少会失效。 
* **render** 包含缓存的HTML字符串，如缓存的页面和区块，可以增大到较大的大小。
* **data** 包含可随路径或类似上下文变化的数据。
* **discovery** 包含缓存的发现数据，用于插件、视图数据(views_data)或Yaml发现的数据（如库信息-library info）。

模块可以通过以下方式在modulename.services.yml文件中定义服务来定义缓存bin（将所需名称替换为“nameofbin”）：
```$xslt
cache.nameofbin:
  class: Drupal\Core\Cache\CacheBackendInterface
  tags:
    - { name: cache.bin }
  factory: cache_factory:get
  arguments: [nameofbin]
```
可以查看[服务](container.md)获取更多信息。

## Deletion(删除)
有两种方式来移除缓存项:
* 永久删除缓存数据可以使用(`delete(), deleteMultiple(), deleteAll()`).
* 使缓存失效,这是一种软删除方式，缓存数据没有真正被删除；包含的方法有(`invalidate(), invalidateMultiple(), invalidateAll()`).
失效的数据，如果需要使用，可以使用`get($cid, $allow_invalid)`，第二个参数设置为TRUE，即可获取到。

## Cache Tags(缓存标签|记)
set()方法的第四个参数可用于指定缓存标记，用于标识每个缓存项中包含哪些数据。
一个缓存项可以有多个缓存标记（一个缓存标记数组），并且每个缓存标记都是一个字符串。
通过约定规则`[prefix]:[suffix]`来生成缓存标记。通常，您需要关联实体或实体列表的缓存标记。您不必为它们手动构造缓存标记。
可以通过`\Drupal\Core\Cache\CacheableDependencyInterface::getCacheTags()`和`\Drupal\Core\Entity\EntityTypeInterface::getListCacheTags()`获取缓存标记。
已标记的数据可以作为一个组失效:无论缓存项的缓存ID（cid）是什么，无论缓存项在哪个缓存bin中存在；只要使用某个缓存标记进行标记，它都将无效。

因此，缓存标签是解决缓存无效问题的一种解决方案:
* 为了使缓存有效，每个缓存项必须在绝对必要时失效。（即最大化缓存命中率。）
* 为了使缓存正确，每次修改某个缓存项时，依赖某个缓存项都必须失效。

一个典型的场景:用户修改了一个出现在两个视图、三个区块和十二个页面上显示的节点。
如果没有缓存标签，我们就不可能知道要使哪些缓存项失效，因此我们必须使所有内容失效:我们必须牺牲有效性来实现正确性。
有了缓存标签，我们可以同时拥有这两个特性。

例:
```$xslt
// A cache item with nodes, users, and some custom module data.
$tags = array(
  'my_custom_tag',
  'node:1',
  'node:3',
  'user:7',
);
\Drupal::cache()
  ->set($cid, $data, CacheBackendInterface::CACHE_PERMANENT, $tags);

// Invalidate all cache items with certain tags.
\Drupal\Core\Cache\Cache::invalidateTags(array(
  'user:1',
));
```
Drupal是一个内容管理系统，因此您自然希望对内容的更改立即反映到任何地方。
这就是为什么我们确保Drupal 8中的每个实体类型都自动支持缓存标记：
保存实体时，可以确保具有相应缓存标记的缓存项都将失效。
当您定义自己的实体类型时也是如此，并能够根据需要覆盖任何默认行为。

可以查看`\Drupal\Core\Cache\CacheableDependencyInterface::getCacheTags(), \Drupal\Core\Entity\EntityTypeInterface::getListCacheTags(), 
\Drupal\Core\Entity\Entity::invalidateTagsOnSave(), \Drupal\Core\Entity\Entity::invalidateTagsOnDelete()等。`

## Cache contexts(缓存上下文)
一些计算数据依赖于上下文数据，例如正在查看页面的登录用户的用户角色、页面渲染的语言、使用的主题等。
缓存此类计算的输出时，必须分别缓存每个变体，以及上下文数据的哪些变体是我们是计算中的信息。

下次需要计算数据时，如果上下文与现有缓存数据集的上下文匹配，则可以重用缓存的数据；如果上下文不匹配，则可以计算并缓存新的数据集以供以后使用。

缓存上下文是被打了标记'cache.context'的服务，这些类实现了`\Drupal\Core\Cache\Context\CacheContextInterface`, 可以查看[缓存上下文](https://www.drupal.org/developing/api/8/cache/contexts)获取更多信息。
包括Drupal核心中存在的上下文列表，以及有关如何定义自己上下文的信息。关于服务方面的信息，可以查看[服务与依赖注入容器](container.md).

通常，缓存上下文是`#cache`的一个渲染属性，可以查看[Render API](render_api.md).

## Configuration(配置)
默认情况下，缓存数据存储在数据库中。但可以对其进行配置，以便所有缓存的数据或单个缓存bin的数据都使用不同的缓存后端（如APCU或memcache）进行存储。

在`settings.php`文件 中，你可以覆写用于特定`cache bin`的服务。
例如，如果你实现了`\Drupal\Core\Cache\CacheBackendInterface`接口的服务叫作`cache.custom`, 
下面一行代码会让Drupal用来存储渲染`cache_render`缓存;

```$xslt
$settings['cache']['bins']['render'] = 'cache.custom';
```

此外，还可以注册默认情况下用于所有缓存箱的缓存实现，方法是：
```$xslt
$settings['cache']['default'] = 'cache.custom';
```

对于存储在数据库中的缓存箱，默认情况下行数限制为5000。这可以为所有数据库缓存箱更改。例如，要将行数限制为50000，请执行以下操作：
```$xslt
$settings['database_cache_max_rows']['default'] = 50000;
```
或每个缓存箱（在本例中，我们允许无限个条目）：
```$xslt
$settings['database_cache_max_rows']['bins']['dynamic_page_cache'] = -1;
```
出于监控的原因，计算存储在表中的数据量可能很有用。可以使用以下SQL代码段：
```$xslt
SELECT table_name AS `Table`, table_rows AS 'Num. of Rows',
ROUND(((data_length + index_length) / 1024 / 1024), 2) `Size in MB` FROM
information_schema.TABLES WHERE table_schema = '***DATABASE_NAME***' AND
table_name LIKE 'cache_%'  ORDER BY (data_length + index_length) DESC
LIMIT 10;
```
最后，您可以将多个缓存后端链接在一起，可以查看`\Drupal\Core\Cache\ChainedFastBackend`和`\Drupal\Core\Cache\BackendChain`

## 相关文档
* \Drupal\Core\Cache\DatabaseBackend
* https://www.drupal.org/node/1884796

## File
core/core.api.php, line 405

Documentation landing page and topics, plus core library hooks.