Services and Dependency Injection Container(服务和依赖注入容器)
=============================================================

本节内容描述服务与依赖注入容器。

## 容器、注入、服务概述
Drupal采用了symfony框架中服务和依赖注入容器概念。定义了一个“服务”(例如访问数据库、发送电子邮件或翻译用户界面文本),
并指定了一个默认类来提供服务。这两个步骤必须一起完成，并且可以由Drupal核心模块或自定义模块完成。
然后，其他模块可以定义替代类来提供相同的服务，从而覆盖默认类。
需要使用服务的类和函数应该总是通过依赖注入容器（也称为“容器”）实例化该类，而不是直接实例化特定的服务提供程序类，以便获得正确的类(默认的或重写的).
可以参考[服务和依赖注入容器](https://www.drupal.org/node/2133171)以获取更多的信息.

## <a name="find_service">发现已存在的服务</a>
Drupal核心在`core.services.yml`文件中定义了许多服务，一些核心模块和第三方模块也在`modulename.services.yml`文件中定义了服务。
API站点罗列了核心提供的服务列表。典型的服务定义像这样:
```
path.alias_manager:
  class: Drupal\Core\Path\AliasManager
  arguments: ['@path.crud', '@path.alias_whitelist', '@language_manager']
```
一些服务像使用其他的服务作为工厂类，典型的服务定义如下:
```
  cache.entity:
    class: Drupal\Core\Cache\CacheBackendInterface
    tags:
      - { name: cache.bin }
    factory: cache_factory:get
    arguments: [entity]
```
第一行代码`cache.entity`表示该服务的名称,大多会以模块的名称为前缀，
可是，为了便于管理，一些服务会以组名开头，比如`cache.*`就是缓存相关的服务，`plugin.manager.*`就是插件管理器组。

class行要么是提供服务使用的默认类的接口，或者服务使用一个工厂类. 如果类依赖于其他服务，argument行将列出依赖的机器名(前缀@); 
当该服务被初始化之前，argument对应的服务将会在容器中进行初始化，并传递到该服务类的构造函数中.

如果工厂本身就是一个服务,那么就可以像上面一样在服务定义中使用。
`factory`也可是一个类，可以在这里面找到[如何使用服务工厂](https://www.drupal.org/node/2133171)信息.

## 通过容器访问服务
如果需要在代码中需要使用服务，则应始终使用服务的机器名通过调用容器来实例化服务类，以便可以重写默认类。
有几种方法可以确保发生这种情况:
* 有关提供服务的类，请参阅本文档的其他部分，描述如何将服务作为参数传递给构造函数。
* 插件类、控制器和其他类似的类具有用于创建类实例的create()或createInstance()方法。
这些方法来自不同的接口，并且有不同的参数，但是它们都包含一个参数$container，类型为\Symfony\Component\DependencyInjection\ContainerInterface。
如果要定义这些类，请在create()或createInstance()方法中，调用$container->get(“myservice.name”)来实例化服务。
这些调用的结果通常传递给类构造函数，并保存为类中的成员变量。
* 对于无法访问通过上述依赖注入服务的方法或类，可以通过调用全局\Drupal类，调用服务的方式来调用依赖注入服务内的函数或类方法。例如:
```$xslt
// Retrieve the entity.manager service object (special method exists).
$manager = \Drupal::entityManager();

// Retrieve the service object for machine name 'foo.bar'.
$foobar = \Drupal::service('foo.bar');
```

注意，上述的代码，如果可能的话，您应该始终使用依赖项注入(通过服务参数或create()/createinstance()方法)来实例化服务，而不是通过\Drupal类来使用,因为:
   * 依赖注入有助于编写单元测试，因为可以模拟容器参数，并且可以使用类构造函数绕过create()方法。如果使用\Drupal类，则单元测试很难编写，并且代码具有更多的依赖性。
   * 在类构造函数和成员变量上具有服务接口,这对于IDE自动完成提醒和自我文档化很有用。
## 定义一个服务
下面是定义一个新的服务的步骤:
* 以模块名为前缀，定义服务名，这个服务名不应该和其他的服务名称重复。
* 创建一个服务接口，用来定义这个服务的用途。
* 为服务创建一个默认实现类，这个实现类实现了上面定义的接口。如果这个实现类需要使用已经存在的服务(比如数据库访问服务),务必在构造函数中使用这些服务参数，
并保存为类的成员变量。 如果需要其他第三方模块服务，则这个在这个模块的依赖属性里面添加第三方模块。 
* 为这个服务添加一个`modulename.services.yml`，其语法可以查看[如何发现服务](#find_service),或参照核心的`*.services.yml`文件定义。

服务也可以被动态定义，但相对于模块来说，这种用法相当少。可以参见`\Drupal\Core\CoreServiceProvider`类。
## 服务标签
一些服务定义里面有服务标签。关于服务标签的用法，可以参考下面.
服务标签用于定义一组相关的服务，或指定服务的某些行为。典型地，如果你定义一个服务标签，你的服务类必须实现相应的接口。一些常见的示例如下:
* **access_check** 表明路由权限检查服务类，可以查看[菜单和路由系统话题](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Menu%21menu.api.php/group/menu/8.6.x)
* **cache.bin** 表明缓存箱服务, 可以查看[缓存话题](https://api.drupal.org/api/drupal/core%21core.api.php/group/cache/8.6.x)
* **event_subscriber** 表明事件订阅服务，事件订阅可以被动态路由或路由修改时使用。也可用于其他目的，可以查看[Symfony事件订阅服务](http://symfony.com/doc/current/cookbook/doctrine/event_listeners_subscribers.html)
* **needs_destruction** 表明在请求操作的最后需要调用一个`destruct()`方法，用于某些目的的析构操作。
* **context_provider** 表明是一个区块上下文提供器，比如用于区块条件。它实现了`\Drupal\Core\Plugin\Context\ContextProviderInterface`。
* **http_client_middleware** 表明服务提供了guzzle中间件，可以查看[中间件处理](https://guzzle.readthedocs.org/en/latest/handlers-and-middleware.html)

为服务创建标记本身并不做任何事情，但是在构建容器时，可以在编译器过程中发现或查询标记，并可以采取相应的操作。
可以查看[\Drupal\Core\Render\MainContent\MainContentRenderersPass](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21MainContent%21MainContentRenderersPass.php/class/MainContentRenderersPass/8.6.x)获取更多信息。

## 覆写服务类
  模块里面可以覆写任何服务的默认类，具体步骤如下:
  * 在模块里面的`src/`目录下，定义一个类名后缀为`*ServiceProvider`的类，比如`MyModuleServiceProvider`。
  * 这个类需要实现`\Drupal\Core\DependencyInjection\ServiceModifierInterface`接口，典型的`\Drupal\Core\DependencyInjection\ServiceProviderBase`类，可以参考或使用。
  * 这个类必须实现一个`alter()`方法. 这个方法告诉Drupal在实际工作中使用上面定义的类而不是默认类。下面是一个例子：
  ```$xslt
  public function alter(ContainerBuilder $container) {
    // Override the language_manager class with a new class.
    $definition = $container->getDefinition('language_manager');
    $definition->setClass('Drupal\my_module\MyLanguageManager');
  }
```
注意: $container是在`\Drupal\Core\DependencyInjection\ContainerBuilder`中被实例化。
## 相关文档
* [https://www.drupal.org/node/2133171](https://www.drupal.org/node/2133171)
* core.services.yml
* \Drupal
* \Symfony\Component\DependencyInjection\ContainerInterface
* Plugin API
* Menu system

## File(文件)
`core/core.api.php`, line 731
Documentation landing page and topics, plus core library hooks.
