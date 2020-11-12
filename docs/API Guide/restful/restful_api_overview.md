RESTful Web Services API 概览
===========================
RESTful Web Services API是Drupal8的新成员，对于每个REST资源，你可以指定支持的动作，并且可以指定序列化格式和认证机制。
>
> 本页描述了REST模块的API的功能，关于如何配置REST资源插件，以及如何创建自己的插件。
>
> 有关如何从应用程序实际使用REST API的信息，请参阅[REST配置和REST请求基础的入门知识](restful_started.md)。

## API 特性
Ordered by most to least frequently used APIs:
#### **Configuring REST resources**
> **特定版本:**
>
> Drupal 8.2之前，通常使用rest.settings.yml文件定义,
> 当您更新到Drupal 8.2或更高版本时，其中的配置会自动迁移。有关详细信息，请参见更改记录：“[REST配置转换成配置实体](https://www.drupal.org/node/2747231)”。

每个REST资源都有一个实现了`\Drupal\rest\RestResourceConfigInterface`接口的`@RestResource`插件配置实体。如果没有这样的配置实体，REST资源插件将无法使用。

每个REST资源都可以在它的配置实体中配置：您可以配置它支持的HTTP方法、序列化格式和身份验证机制。所有方法都支持相同的序列化格式和身份验证机制。

安装[REST UI](https://www.drupal.org/project/restui)模块来配置REST资源。(或者,如果要手动修改和导入YAML配置，请参照`core/modules/rest/config/optional/rest.resource.entity.node.yml`！）

#### **Resource Plugins(资源插件)**
`\Drupal\rest\Plugin\ResourceInterface`: 资源插件,通过REST插件暴露额外的信息.
* 插件必须使用`@RestResource`注释，便于插件被发现。
* 可以使用`hook_rest_resource_alter`修改插件注释元数据。
* `\Drupal\rest\Plugin\ResourceBase`提供了接口的默认实现 ，因此资源插件中可不必须实现每一个方法。

上面允许Drupal使用REST、使用任何身份验证机制（请参阅[身份验证API](https://www.drupal.org/developing/api/8/authentication)）
和任何序列化格式（包括编码和规范化-有关详细信息，请参阅[序列化API](https://www.drupal.org/developing/api/8/serialization)）自动公开该资源。
因此，作为开发人员，您只需要实现需要公开资源对象的逻辑。


## <a name="practical">实践: 怎样使用REST？</a>
通常，您将实体公开为REST资源，以构建一个分离的Drupal站点，让本地移动iOS/Android应用程序使用Drupal站点信息或提供给Drupal站点，或者集成一些Web服务。
>
> 有关API优先的更多的信息，[可以查看这里](https://www.drupal.org/project/issues/search?issue_tags=API-First%20Initiative),
> 这里删除了`Accept-header`的支持-您始终需要在URL参数里面添加_format='xxx', xxx代表JSON，或其他格式。

#### **配置REST资源**
公开所需的REST资源-请参见上面的配置REST资源。
最常见的用例是与实体交互。对于公开实体的REST资源，
[Entity Access API](https://www.drupal.org/docs/8/api/entity-api/access-on-entities-tbd)确定实体是否可以与之交互。
例如，用户必须具有访问内容权限才能获取节点实体，以及创建项目内容权限才能发布项目节点。

>在Drupal 8.2之前，必须在必要的实体类型特定权限的基础上授予REST特定权限。
>有关详细信息，请参见更改记录：“[通过REST访问实体不再需要其他特定于REST的权限](https://www.drupal.org/node/2733435)”。

#### **Customize a REST resource's formats(自定义REST资源格式)**
默认情况下，REST模块支持JSON和XML。如果安装了核心的hal模块，还可以启用hal_json格式。
通过安装其他模块，您可以访问更多的格式-有关详细信息，请参阅[序列化API](https://www.drupal.org/developing/api/8/serialization）。

通过REST UI模块修改，或者直接修改配置实体:
```
granularity: resource
configuration:
  methods:
    - …
  formats:
    - hal_json
    - xml
    - json
  authentication:
    - …
```
#### Customize a REST resource's authentication mechanism(自定义REST资源的认证机制)
对于认证用户和逐步分离的Drupal站点，您可能希望使用认证用户已经拥有的cookie进行认证。

通过使用REST UI模块，或直接修改配置:
```
granularity: resource
configuration:
  methods:
    - …
  formats:
    - …
  authentication:
    - cookie
```
#### Creating REST resource plugins(创建REST资源插件)
简单示例`\Drupal\dblog\Plugin\rest\resource\DBLogResource`

复杂示例`\Drupal\rest\Plugin\rest\resource\EntityResource`

也可以通过`Drupal Console command `自动生成: `drupal generate:plugin:rest:resource`

TODO

最重要的是`@RestResource`注释定义里面的`uri_paths`定义(它会使用[链接关系类型](https://api.drupal.org/api/drupal/core%21core.link_relation_types.yml/8.3.x)作为键，
并且部分URIs作为值). 如果没有指定，Drupal会根据插件ID自动生成URLs.
例如,插件ID为`fancy_todo`时，就会生成`GET|PATCH|DELETE  /fancy_todo/{id}`和`POST /fancy_todo`。
但是，通常想指定一个自定义的详情页`canonical`路径，例如，`/todo/{todo_id}`,
```
 *   uri_paths = {
 *     "canonical" = "/todo/{id}",
 *     "https://www.drupal.org/link-relations/create" = "/todo"
 *   }
```
也可以标记一个版本号
```
*   uri_paths = {
 *     "canonical" = "/api/v1//todo/{id}",
 *     "https://www.drupal.org/link-relations/create" = "/api/v1/todo"
 *   }
```

## 相关内容

* [授权API](https://www.drupal.org/docs/8/api/authentication-api)
* [序列化](https://www.drupal.org/docs/8/core/modules/serialization)













