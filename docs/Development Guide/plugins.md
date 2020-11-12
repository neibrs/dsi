Plugins(插件)
=============

## 概述和术语

插件的基本思想是：允许模块以可扩展的、面向对象的方式提供功能。
**控制模块**为功能定义基本框架(接口)，其他模块创建具有特定行为的插件(实现接口)。
**控制模块**根据需要实例化现有插件，并调用相应的方法来使用它们的功能。
平台核心使用插件的示例包括：block(区块系统)(区块类型为插件)、entity(实体)/field(字段)系统(实体类型、字段类型、字段格式器和字段部件是插件)、图像处理系统(图像效果和图像工具包是插件)和搜索系统(搜索页面类型是插件)。

插件根据插件类型进行分组,每个类型由接口进行定义(例如BlockPluginInterface)。
每个插件类型都由插件管理器服务进行管理(例如BlockManager)，这些插件管理器服务使用插件发现方法来查找该类型提供的插件，并使用plugin factory(插件工厂)对其进行实例化。    

插件类型使用了以下概念或组件：
* **Plugin derivatives(插件派生)**:允许一个插件类作为多个插件显示自己。
例如：菜单模块通过区块插件派生为每个定义的菜单提供一个区块(\Drupal\system\Plugin\Derivative\SystemMenuBlock)。
* **Plugin mapping(Plugin Mappers插件映射)**:允许插件类将配置字符串映射到实例，并让插件自动实例化，而无需编写其他代码。  
插件映射器允许您将某些内容(通常是字符串)映射到特定的插件实例。使用此方法的插件类型可以基于任意定义的名称返回完全配置和实例化的插件，而不需要使用此API的开发人员手动实例化和配置插件实例。 
* **Plugin collections(插件集合)**:提供一种从单个插件定义一次性实例化一组插件实例的方法。  
* **Discovery Decorators(发现装饰器)**: 发现装饰器是另一种可用的发现方法，用于包装现有的发现方法；核心目前提供了**CacheDecorator**,这个类将缓存插件类型的发现过程。如果需要，这个模式可以扩展到其他用例。

**插件系统包含三个基本元素：**
1. **Plugin Type(插件类型)**   
插件类型是中央控制类，定义了如何发现和实例化此类型的插件。该插件类型描述该类型的所有插件的主要用途，例如缓存后端、图像操作、区块等。
1. **Plugin Discovery(插件发现)**    
插件发现是在可用的代码中查找特定插件类型的插件的过程。
3. **Plugin Factory(插件工厂)** 插件工厂负责实例化为给定的插件。 模块开发人员可能需要对插件执行以下几项操作：
    * **定义一个全新的插件类型**
    * **创建一个已经存在类型的插件**
    * **执行插件的任务功能**

在[https://www.drupal.org/developing/api/8/plugins](https://www.drupal.org/developing/api/8/plugins)查看关于插件系统更多的信息。
* Block API
* Entity API
* Various types of field-related plugins
* Views plugins (has links to topics covering various specific types of Views plugins).
* Search page plugins

## 定义一个全新的插件类型
* 为插件定义一个接口。该接口描述了一系列通用的行为，方法。通常这个接口会实现一个或多个下列的接口。
    * \Drupal\Component\Plugin\PluginInspectionInterface
    * \Drupal\Component\Plugin\ConfigurablePluginInterface
    * \Drupal\Component\Plugin\ContextAwarePluginInterface
    * \Drupal\Core\Plugin\PluginFormInterface
    * \Drupal\Core\Executable\ExecutableInterface
* (可选) 创建一个提供接口部分实现的基类，以方便的开发人员创建该类型的插件。这个基类通常继承了**\Drupal\Core\Plugin\PluginBase**，或者一个实现了这个基类的扩展基类。
* 为插件发现选择一个方法，如果必要定义一个类，查看下面的**Plugin Discovery**
* 创建一个用来发现和初始化插件的管理器/工厂和服务。查看**插件管理器和服务的定义**
* 使用插件管理器初始化插件。在插件接口中调用该插件类型插件的功能。
* (可选)如果合适，定义一个插件集合。有关详细信息，请参阅下面的@ref sub_collection。

## Plugin discovery(插件发现)

插件发现是插件管理器用来发现由模块和其他模块定义的某类型插件的过程。插件发现方法是实现**\drupal\component\plugin\discovery\discoveryInterface**的类。大多数插件类型使用以下发现机制之一：
* **Annotation**:插件类被注释并放置在定义的命名空间子目录中。大多数Drupal核心插件都使用这种发现方法。
* **Hook**: 插件模块需要实现一个钩子，这个钩子就是告诉插件管理器关于这个插件的信息。
* **YAML**: 插件被放在YAML文件中，Drupal核心就是使用这个方法来发现`local tasks`和`local actions`的，如果所有的插件都使用相同的类，那么它有点像插件派生.
* **Static**: 插件管理器本身注册，静态发现仅用于模块不能定义新类型的插件的时候(如果插件列表是静态的)。

也可以自定义发现机制或将方法混合在一起。还有更多的细节，比如注释装饰器，可以应用到一些发现方法中。有关更多详细信息，请参阅https://www.drupal.org/developing/api/8/plugins。  
下面以文档注释为主要介绍对象，因为这是通用的插件发现方法。
**定义一个插件管理器类和服务**
* 为插件选择一个名称空间子目录。例如，搜索页面插件进入模块命名空间下的目录Plugin/Search。
* 为插件类型定义注释类。这个类应该扩展\Drupal\Component\Annotation\Plugin，对于大多数插件类型，它应该包含注释插件相对应的成员变量。所有插件都至少有$id:一个唯一的字符串标识符。
* 定义一个用于更改发现的插件定义的alter hook。您应该在*.api.php文件中记录钩子。
* 定义一个插件管理器类，这个类应该实现\Drupal\Component\Plugin\PluginManagerInterface，并且大部分的插件管理器类都会扩展\Drupal\Core\Plugin\DefaultPluginManager，您可能需要定义的唯一方法是类构造函数，它需要调用父构造函数来提供有关注释类和插件命名空间的信息以进行发现，设置alter hook，并有可能设置缓存。有关示例，请参见扩展了Defaultpluginmanager的类。
* 为插件管理器定义服务。有关详细信息，请参阅服务主题。您的服务定义应该如下所示，引用您的管理器类和父(默认)插件管理器服务来继承构造函数参数：
```
plugin.manager.mymodule:  
  class:  
    Drupal\mymodule\MyPluginManager
    parent: default_plugin_manager
```
* 如果您的插件是可配置的，那么您还需要定义配置模式，可能还需要定义配置实体类型。有关详细信息，请参阅[配置API](https://api.drupal.org/api/drupal/core%21core.api.php/group/config_api/8.6.x)主题。

**定义插件集合**
一些可配置的插件类型允许管理员为每个插件创建零个或多个实例，每个实例都有自己的配置。例如，一个单独的区块插件可以多次配置不同的可见性、不同的标题或其他特定设置，用以在主题的不同区域显示。因此，该插件类型就是插件集合概念。  
插件集合的类扩展自\Drupal\Component\Plugin\LazyPluginCollection或者子类；核心有多个示例。如果你定义的插件类型使用了插件类型，通常它会有一个配置实体，并且实例类实现了\Drupal\Core\Entity\EntityWithPluginCollectionInterface. 核心也有相应的示例，可以查看[Configuration API topic](https://api.drupal.org/api/drupal/core%21core.api.php/group/config_api/8.6.x)更多信息。

## 创建已存在插件类型的插件
假设插件类型使用基于注释的发现机制，为了创建一个已存在插件类型的插件，必须创建这样一个类:
* 实现插件接口，因此实现必要的方法。通常必须实现插件基类的方法，如果这个插件基类已提供的话。
* 在文档头部添加正确的插件注释。查看[Annotation topic](https://api.drupal.org/api/drupal/core%21core.api.php/group/annotation/8.6.x)更多的信息。
* 给出正确的插件命名空间，以便被插件机制发现。

通常最简单的方式是复制一个已有的插件来改写。  
也可以创建一个插件派生类，它允许您的插件类以多个插件的形式呈现给用户界面。为了实现这一点，需要创建一个独立的插件派生类，这个派生类实现了\Drupal\Component\Plugin\Derivative\DerivativeInterface接口；可以查看这个示例\Drupal\system\Plugin\Block\SystemMenuBlock(插件类)和\Drupal\system\Plugin\Derivative\SystemMenuBlock(派生类)

## 执行插件的任务
执行插件的任务可以分为下面一些步骤:
* 定位插件管理器服务的名称，并且实例化一个服务。
* 在插件管理器类中，使用诸如getDefinitions(),getDefinition(),或其他特定的方法来获取特定插件或全部已定义插件的信息。
* 调用插件管理器类中的createInstance()方法实例化一个插件对象。
* 使用插件对象的执行相关的方法。


## 补充文档
## Plugin, Tagged Service or Service?
**Plugin & tagged services**
插件和标记服务都是通过公共接口实现不同的行为。 例如，图像转换。常见的图像转换包括缩放、裁剪、去饱和等。每种转换类型对同一数据的作用方式都相同; 它接受一个图像文件，执行转换，然后返回一个更改过的图像。然而，每种效果都是不同的。
如果用户需要选择和/或配置某个行为，请使用插件。如果不需要用户交互，请使用标记服务。

**Service**
服务提供了相同的功能，并且服务是可以[更改、交换、重写](https://www.drupal.org/node/2026959)的，不同服务之间的区别仅仅是内部的实现不同。  
比如缓存。缓存应该提供get、set和expire方法。用户只需要一个缓存，一个缓存应该能够替换另一个缓存，而不会有任何功能上的差异。这些方法的内部实现及其使用的机制可能大不相同。
在这种情况下，使用服务定义更合适。

**Annotation-Based Plugins in Views**
在视图中基于注释的插件。Drupal8核心中的视图大多都是使用的此方法。更多细节请参看[Annotation-Based Plugins in Views](https://www.drupal.org/docs/8/api/plugin-api/annotation-based-plugins-in-views)

** Plugin Contexts(插件上下文)
有时插件需要另一个对象来执行其主操作。这被称为插件上下文。使用一个实际的例子，几乎所有的条件插件都需要一个上下文。让我们看看nodeType条件插件的定义。

```
/**
 * Provides a 'Node Type' condition.
 *
 * @Condition(
 *   id = "node_type",
 *   label = @Translation("Node Bundle"),
 *   context = {
 *     "node" = @ContextDefinition("entity:node", label = @Translation("Node"))
 *   }
 * )
 *
 */
```
此插件定义中有3个键，首先是必需的“id”，然后是用户界面的“label”，最后是“context”数组。上下文键存储在一个名为(ContextDefinition)上下文定义数组，条件需要该数组才能执行其“evaluate()”方法。对于nodeType条件，必须有一个Node才能检查其类型。nodeType::evaluate()方法演示了如何在实践中工作：

```
  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    if (empty($this->configuration['bundles']) && !$this->isNegated()) {
      return TRUE;
    }
    $node = $this->getContextValue('node');
    return !empty($this->configuration['bundles'][$node->getType()]);
  }
```

##参考
[Drupal API](https://api.drupal.org/api/drupal/core%21core.api.php/group/plugin_api)