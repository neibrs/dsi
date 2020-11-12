Entity(实体)
===========

本文档描述了如何定义和管理content entity(内容实体)和config entity(配置实体)。

Entity是持久保存content(内容)和config(配置)的对象。参考[信息类型](info_types.md)和[Configuration(配置)](config_api.md)。

每个entity均属于一个特定的entity type(实体类型)。一些content entity(内容实体)有子类型，称着`bundles`，没有子类型的内容实体则有一个单一的bundle。
例如，用于内容页的node实体类型具有称为“内容类型”的`bundle`，而用于用户帐户的user实体仅具有一个`bundle`。

下面的信息列举了更多关于实体和实体API的信息。可以查看关于[实体的开发文档](https://www.drupal.org/developing/api/entity)

### 定义实体类型
实体类型是在模块中使用Drupal插件API的方式来定义的，有关插件API的信息可参看[这里](plugins.md).

下面是定义一个实体类型的步骤:
* 选择一个机器名，通常是模块名或者以模块名打头。尽可能短，最长不超过32个字符。
* 为实体定义一个接口，通常包括get/set方法， 并且这个接口扩展其中一个类型接口[\Drupal\Core\Config\Entity\ConfigEntityInterface](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Config%21Entity%21ConfigEntityInterface.php/interface/ConfigEntityInterface/8.6.x)或者[\Drupal\Core\Entity\ContentEntityInterface](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21ContentEntityInterface.php/interface/ContentEntityInterface/8.6.x).
* 为实体定义一个类，并实现上一步的接口，并且实体类扩展核心的[\Drupal\Core\Config\Entity\ConfigEntityBase](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Config%21Entity%21ConfigEntityBase.php/class/ConfigEntityBase/8.6.x)或[\Drupal\Core\Entity\ContentEntityBase](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21ContentEntityBase.php/class/ContentEntityBase/8.6.x).
并且在类的顶部添加上`@ConfitEntityType`或`@ContentEntityType`的注释块。 推荐扩展`\Drupal\Core\Entity\EditorialContentityBase`类，以便对实体API的修订和发布功能提供开箱即用的支持，这将允许您的实体类型与内容审核模块提供的drupal的编辑工作流一起使用。
* 在注释中，ID即为该实体类型的机器名,并且label给定了实体类型的可读名称。如果当前实体有`bundle`,则`bundle_label`则是`bundle`的可读名称。
* 注释内有几个句柄类需要注意和定义:
  * list_builder: 定义一个扩展自[\Drupal\Core\Config\Entity\ConfigEntityListBuilder](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Config%21Entity%21ConfigEntityListBuilder.php/class/ConfigEntityListBuilder/8.6.x)(内容实体)或[\Drupal\Core\Entity\EntityListBuilder](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21EntityListBuilder.php/class/EntityListBuilder/8.6.x),以提供管理实体的列表。
  * add,edit,default表单，定义一个或两个扩展自`\Drupal\Core\Entity\EntityForm`的类，这些类用来提供实体的添加和编辑表单功能。对于内容实体，基类`\Drupal\Core\Entity\ContentEntityForm`更好。
  * delete form，该类使用`\Drupal\Core\Entity\EntityConfirmFormBase`的类或子类，提供删除实体的表单。
  * view_builder: 查看详情页时需要用到此类，此类扩展自[\Drupal\Core\Entity\EntityViewBuilderInterface](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21EntityViewBuilderInterface.php/interface/EntityViewBuilderInterface/8.6.x),通常扩展[\Drupal\Core\Entity\EntityViewBuilder](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21EntityViewBuilder.php/class/EntityViewBuilder/8.6.x).
  * translation: 配置实体不需要此句柄类，内容实体才需要。如果注释内有`translatable`为`TRUE`，则此句柄类需要扩展自[Drupal\content_translation\ContentTranslationHandler](https://api.drupal.org/api/drupal/core%21modules%21content_translation%21src%21ContentTranslationHandler.php/class/ContentTranslationHandler/8.6.x).
  * access: 如果你的配置实体需要复杂的权限控制，则需要此句柄扩展[\Drupal\Core\Entity\EntityAccessControlHandlerInterface](https://api.drupal.org/api/drupal/core%21modules%21content_translation%21src%21ContentTranslationHandler.php/class/ContentTranslationHandler/8.6.x),并且需要覆写`checkAccess()`方法和`checkCreateAccess()`方法；而不是`access()`方法。
  * storage: 此类扩展自[\Drupal\Core\Entity\EntityStorageInterface](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21EntityStorageInterface.php/interface/EntityStorageInterface/8.6.x), 如果没有指定，内容实体默认使用[\Drupal\Core\Entity\Sql\SqlContentEntityStorage](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21Sql%21SqlContentEntityStorage.php/class/SqlContentEntityStorage/8.6.x), 配置实体默认使用[\Drupal\Core\Config\Entity\ConfigEntityStorage](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Config%21Entity%21ConfigEntityStorage.php/class/ConfigEntityStorage/8.6.x), 也可以扩展其中一个，添加自定义的行为。
  * views_data: 为实体类型添加视图数据,该类实现了[\Drupal\views\EntityViewsDataInterface](https://api.drupal.org/api/drupal/core%21modules%21views%21src%21EntityViewsDataInterface.php/interface/EntityViewsDataInterface/8.6.x), 通常，代码生成器会自动使用一个扩展[\Drupal\views\EntityViewsData](https://api.drupal.org/api/drupal/core%21modules%21views%21src%21EntityViewsData.php/class/EntityViewsData/8.6.x)的类。
* 对于内容实体，注释里面将指定数据存放的数据表和字段。这些注释属性包括`base_table`, `data_table`, 'entity_keys`, 等等， 都在[\Drupal\Core\Entity\EntityType](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21EntityType.php/class/EntityType/8.6.x)中定义。
* For content entities that are displayed on their own pages, the annotation will refer to a 'uri_callback' function, which takes an object of the entity interface you have defined as its parameter, and returns routing information for the entity page; see node_uri() for an example. You will also need to add a corresponding route to your module's routing.yml file; see the entity.node.canonical route in node.routing.yml for an example, and see Entity routes below for some notes.
* 可选项，替代`*.routings.yml`文件中定义路由，可以自动由路由处理器自动产生路由。可查看[实体路由](#entity_routes)。否则，可以自定义不同的实体路由和链接。可查看实体的`links`属性的定义，该属性里面定义了URL路径模板。与之对应的路径包括下面的路由名:
    *  `entity.$entity_type_id.$link_template_type`. 可查看下面的[实体路由](#entity_routes)获取实体路由定义相关的更多信息。典型的链接包括如下:
        *  canonical: 即详情页，如果实体有单独的显示页，那么canonical即为默认的显示或实体编辑页。
        *  delete-form: 确认实体删除页面.
        *  edit-form: 编辑页面.
        *  其他的链接类型也可以在这里自定义.
* 如果你定义的实体的字段是可配置的，那么在注释中应该提供一个`field_ui_base_route`的注释定义,并给定一个可字段配置的URL路径，这个路径下可管理字段，管理字段显示，和管理表单的字段显示等等。通常是在bundle设置页面，或实体没有bundle时在实体类型设置页面。
* 如果你的实体类型有bundle，也可以定义一个第二插件来处理bundles, 这个插件自身就是一个配置实体类型,因此下面的可以使用下面的几个步骤来定义: 配置实体的机器名被指定到当前实体的`bundle_entity_type`键值内。例如，Node实体,Node的bundle类是`\Drupal\node\Entity\NodeType`,这个类的机器名为`node_type`. `\Drupal\node\Entity\Node`类的`bundle_entity_type`的值为机器名`node_type`, 而配置实体的bundle内的一定会定义一个`bundle_of`的属性，这个名字应该是不会再更改的永久的名字。
* 在实体类中可以看额外的注释属性，比如`\Drupal\node\Entity\Node`(内容实体)和`\Drupal\user\Entity\Role`(配置实体). 实体类型的注释属性可以在[\Drupal\Core\Entity\EntityType](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21EntityType.php/class/EntityType/8.6.x)中可查看到。

### <a name="entity_routes">Entity Routes实体路由</a>
实体路由可以在`*.routing.yml`文件中定义，可以查看[Routing API](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Routing%21routing.api.php/group/routing/8.6.x),以获取更多信息。实体路由的其他选项可以通过路由提供器类定义，并且在实体类的注释中引用。可参看本文档的最后部分。

也可能使用YAML文件和路由提供器类一起提供实体路由。为避免这两种方式提供的路由重复，YAML文件定义的路由优先使用，删除路由提供器的路由定义。
下面是一个YAML路由示例，为区块配置表单的路由:
```
entity.block.edit_form:
  path: '/admin/structure/block/manage/{block}'
  defaults:
    _entity_form: 'block.default'
    _title: 'Configure block'
  requirements:
    _entity_access: 'block.update'
```
路由定义的提示:
* path: {block}在路径中是一个占位符,这个通常是实体的机器名.在URL中，占位符所代表的通常是实体的ID，当路由被加载时，这个ID会被实体路由系统加载对应的实体，并传递一个实体对象到路由控制器内。
* defaults: 实体表单路由通常使用`_entity_form`代替`_controller`或`_form`，这个值由实体类型和实体注释中定义的表单句柄组成; 因此，在本示例中，`block.default`引用到block实体类型定义的`default`表单处理器。 这个处理句柄如下:
```
  handlers = {
    "form" = {
      "default" = "Drupal\block\BlockForm",
```

如果你想使用路由提供器类代替routingYAML文件:
* [\Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21Routing%21DefaultHtmlRouteProvider.php/class/DefaultHtmlRouteProvider/8.6.x)提供详情、编辑表单和删除表单路由。
* [\Drupal\Core\Entity\Routing\AdminHtmlRouteProvider](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21Routing%21AdminHtmlRouteProvider.php/class/AdminHtmlRouteProvider/8.6.x)提供了相同的路由，并提供了管理员界面的编辑和删除页面。
* 也可以创建自定义的类用来修改部分的行为，但扩展类必须继承上面的其中一个。

为了使用这个自定义的类，需要在实体类的注释内，作如下处理:
```
handlers = {
  "route_provider" = {
    "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
```

### 定义一个内容实体Bundle
实体类型如果使用bundle, 比如Node(bundle是内容类型)和Taxonomy(bundle是vocabularies)，
模块和Profile文件可以在config/install目录使用默认配置提供bundles,
可以参看[Configuration API-配置API](https://api.drupal.org/api/drupal/core%21core.api.php/group/config_api/8.6.x).
Drupal核心中有几个好的示例可供参考:
* Forum(论坛)模块定义了一个`node.type.forum.yml`的文件和Vocabulary(词汇)`taxonomy.vocabulary.forums.yml`文件。
* Book模块定义了一个`node.type.book.yml`。
* Standard Profile(标准安装文件)提供了页面和文档内容类型的定义，
    * 在`node.type.page.yml`和`node.type.article.yml`中，
    * Tags的词汇定义在`taxonomy.vocabulary.tags.yml`，
    * Node评论类型定义在`comment.type.comment.yml`，
    这个Profile文件的配置特别有指导意义，因为它还向文章类型添加了几个字段，并为节点类型设置了视图和表单显示模式。

### 加载、查询和实体显示输出
使用实体存在管理器(EntityStorageManager)，这个类实现了`\Drupal\Core\Entity\EntityStorageInterface`的接口，可以通过如下方式获得:
```
$storage = \Drupal::entityManager()
  ->getStorage('your_entity_type');

// Or if you have a $container variable:
$storage = $container
  ->get('entity.manager')
  ->getStorage('your_entity_type');
```
`your_entity_type`是你定义实体的机器名(指实体定义的注释文档内的id的值). 
获取实体管理器的方式尽量使用依赖注入的方式获取对象。
可以参看[服务与依赖注入](https://api.drupal.org/api/drupal/core%21core.api.php/group/container/8.6.x)获取更多的信息。

实体输出显示，前提是需要找到该实体，查找实体需要使用`entity query`, 
这个对象实现了`\Drupal\Core\Entity\Query\QueryInterface`的接口，可以通过如下方式获得:
```
// Simple query:
$query = \Drupal::entityQuery('your_entity_type');

// Or, if you have a $container variable:
$storage = $container
  ->get('entity_type.manager')
  ->getStorage('your_entity_type');
$query = $storage
  ->getQuery();
// 如果需要使用聚合的方式，那么聚合对象应是实现了\Drupal\Core\Entity\Query\QueryAggregateInterface:


$query \Drupal::entityQueryAggregate('your_entity_type');
// Or:
$query = $storage->getAggregateQuery('your_entity_type');
```
在上面的示例中，可以添加条件到查询中,比如方法: condition(), exists()等等,如果需要可以添加排序, 分页，和Range。
通过entity query查询返回的是实体IDs. 下面的示例演示了核心的文件实体:
```
$fids = Drupal::entityQuery('file')
  ->condition('status', FILE_STATUS_PERMANENT, '<>')
  ->condition('changed', REQUEST_TIME - $age, '<')
  ->range(0, 100)
  ->execute();
$files = $storage
  ->loadMultiple($fids);
```
常规的实体查看方式上面已经介绍。如果因为特殊的原因，需要使用代码输出一个实体的特定显示模式的内容，
那么你可以使用实体显示构造器(Entity view builder),这个对象实现了`\Drupal\Core\Entity\EntityViewBuilderInterface`, 可以通过这种方式获取:
```
$view_builder = \Drupal::entityManager()
  ->getViewBuilder('your_entity_type');

// Or if you have a $container variable:
$view_builder = $container
  ->get('entity.manager')
  ->getViewBuilder('your_entity_type');
```
然后，构建和输出实体:
```
// You can omit the language ID, by default the current content language will
// be used. If no translation is available for the current language, fallback
// rules will be used.
$build = $view_builder
  ->view($entity, 'view_mode_name', $language
  ->getId());

// $build is a render array.
$rendered = \Drupal::service('renderer')
  ->render($build);
```

### 实体的权限检查
实体类型中定义了权限检查模式，实体的访问权限相当的复杂，所以您不应该假设任何特定的权限方案。一旦你的实体被加载，就可以检查实体或字段特定操作的权限(比如'view'查看）。
```
$entity
  ->access($operation);
$entity->nameOfField
  ->access($operation);
```
实体的访问检查对象实现了`\Drupal\Core\Access\AccessibleInterface`接口。
实体的默认权限检查调用了两个钩子，第一个是`hook_entity_access()`，第二个是`hook_ENTITY_TYPE_access()`(这里ENTITY_TYPE是实体的机器名). 如果这两个钩子调用后，没有模块返回TRUE或FALSE，那么实体的默认权限检查将会起作用。例如，如果实体是创建，那么`hook_entity_create_access()`和`hook_ENTITY_TYPE_create_access()`将会起作用。

Node实体类型有一个复杂的权限检查系统，它允许开发者交互或使用。这一部分在[Node access topic](https://api.drupal.org/api/drupal/core%21modules%21node%21node.module/group/node_access/8.6.x)

### 也可查看
* [多语种-国际化翻译](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Language%21language.api.php/group/i18n/8.6.x)
* [实体CRUD,编辑和查看等钩子](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21entity.api.php/group/entity_crud/8.6.x)
* [\Drupal\Core\Entity\EntityManagerInterface::getTranslationFromContext()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21EntityRepositoryInterface.php/function/EntityRepositoryInterface%3A%3AgetTranslationFromContext/8.6.x)

### File(文件)
core/lib/Drupal/Core/Entity/entity.api.php, line 325
包含实体的钩子和其他文档。
