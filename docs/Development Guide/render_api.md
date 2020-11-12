Render API 概览
=================

本节描述了主题系统和渲染(Render)API相关内容。

Drupal的主题系统的主要目的是让主题完全控制站点的外观，
包括从HTTP请求返回的标记和用于设置标记样式的CSS文件。
为了确保主题可以完全控制标记，模块开发人员应避免直接为页面、区块和其他用户可见的输出编写HTML标记，
而应返回结构化的“渲染数组”（请参见下面的渲染数组）。这样做还可以提高可用性，因为可以确保在站点不同区域显示类似功能的标记是相同的，
从而减少用户需要学习的用户界面模式。

为了更深入的了解主题和渲染APIs，可以查看以下三个方面:
* https://www.drupal.org/docs/8/theming
* https://www.drupal.org/developing/api/8/render
* [Theme system overview](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21theme.api.php/group/themeable/8.6.x).(主题系统概览)


## Render arrays(渲染数组)
渲染API的核心结构是渲染数组，它是一个层次关联数组，包含需要渲染的数据以及描述如何渲染数组的属性。
函数返回的用于指定要发送到Web浏览器或其他数据最终将通过drupal_render()调用来渲染，
该调用将在适当的情况下通过渲染数组层次结构进行递归，从而调用主题系统来执行实际的渲染。
如果函数或方法实际需要返回渲染的输出而不是渲染数组，则最佳做法是创建渲染数组，
通过调用drupal_render()渲染它，并返回该结果，而不是直接写入标记。有关渲染过程的详细信息，请参见drupal_render()的文档。

渲染数组(包括最外面的数组)层次结构中的每一层都有一个或多个数组元素。
名称以“#”开头的数组元素称为“属性(properties)”，其他名称的数组元素称为“子级”(构成层次结构的下一级);
子级的名称是灵活的，而属性名称是渲染API的特定数据类型。渲染数组的特例是表单数组，它指定了HTML的表单元素；
有关表单的详细信息，请参见[表单主题](form_api.md)。

渲染数组(在层次结构的任何层级中)通常定义以下属性之一：
* `#type` 指定了数据包含的数据，`render element`选项类型(例，'form': html表单， 'textfield', 'submit': html表单元素，'table'), 可以查看[Render Elemennts](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21theme.api.php/group/theme_render/8.6.x#elements)
* `#theme` 指定了需要被特殊处理的主题钩子。模块通过`hook_theme()`定义主题钩子， 钩子里面的`variables`提供可显示的内容。如果`hook_theme()`指定了一个变量`foo`, 那么在渲染数组中的使用方式应该像这样`#foo`.
* `#markup`  为HTML直接提供渲染元素，除非输出内容非常简单，最好使用`#theme`和`#type`, 
因此主题可以自定义标记(markup)。请注意，markup标签的值会传递到 `\Drupal\Component\Utility\Xss::filterAdmin()`方法中，进行XSS安全转换。
(比如`<script><style>`之类的是不允许的)。查看允许的标签列表(`\Drupal\Component\Utility\Xss::$adminTags`),
如果你需要输出任何不在安全标签内的html标签，可以实现一个主题钩子或者资源库(asset library)，
或者你也可以使用`#allowed_tags`来指定安全标签。
* `#plain_text`  指定数组提供需要转义的文本。此值优先于标记(#markup)。
* `#allowed_tags` 如果提供了标记(#markup)，则可以使用它更改中标签允许的标签。该值是`Xss:filter()`将接受的标签数组。如果设置了纯文本(`#plain_text`)，则忽略此值。


## Render elements(渲染元素)
核心和各模块提供了渲染元素的定义。定义的主要方式是创建一个渲染元素插件。这里有两种类型的渲染元素:
* **通用元素** 通用渲染元素插件实现了`\Drupal\Core\Render\Element\ElementInterface`，
并扩展了`\Drupal\Core\Render\Element\RenderElement`基类，
并且使用了`\Drupal\Core\Render\Annotation\RenderElement`注释，该类位于`Plugin/Element`目录下
* **表单输入元素** 指表单元素控件的渲染元素。这些类实现了`\Drupal\Core\Render\Element\FormElementInterface`接口，
并扩展了`\Drupal\Core\Render\Element\FormElement`基类,
并使用`\Drupal\Core\Render\Annotation\FormElement`注释。

## Caching(缓存)
Drupal渲染过程可以缓存已渲染过的渲染数组的任何层级的数据，这可以减少昂贵的计算，并加速页面加载。有关缓存相关的信息可以查看[缓存API](cache_api.md)。

为了使缓存成为可能，需要提供以下信息：
*  **Cache keys** 渲染数组可缓存部分的标识符。对于渲染过程中涉及昂贵计算的渲染数组部分，应该创建并添加缓存标识符。
*  **Cache contexts** 上下文可以影响渲染，比如用户角色和语言。如果未指定上下文，则表示渲染数组不随任何上下文而变化。
*  **Cache tags** 渲染所依赖的数据标记，例如单个节点或用户帐户的标记，以便当这些更改时，缓存可以自动失效。
如果数据由实体组成，则可以使用`\Drupal\Core\Entity\EntityInterface::getCacheTags()`生成合适的标记；配置对象具有类似的方法。
*  **Cache max-age** 可缓存渲染数组的最大持续时间。默认为`\Drupal\Core\Cache\Cache::PERMANENT`(永久可缓存)。

缓存信息在渲染数组的缓存属性(#cache)中提供。在此属性中，如果渲染数组随上下文而变化、依赖于某些可修改的数据或依赖于仅在有限时间内有效的信息，
则始终提供缓存上下文、标记和最大使用时间。只应在应缓存的渲染数组部分设置缓存键。
上下文将自动替换为当前请求的值（例如，当前语言），并与键组合以形成缓存ID(缓存标识符)。
缓存上下文、标记和最大期限将向上传播到渲染数组层次结构，以确定包含渲染数组部分的可缓存性。

下面是一个关于`#cache`键可能包含的属性值:
```$xslt
  '#cache' => [
    'keys' => ['entity_view', 'node', $node->id()],
    'contexts' => ['languages'],
    'tags' => $node->getCacheTags(),
    'max-age' => Cache::PERMANENT,
  ],
```
在[响应级别](https://www.drupal.org/developing/api/8/render/arrays/cacheability)里，你可以看到`X-Drupal-Cache-Contexts and X-Drupal-Cache-Tags`头信息。

## Attaching libraries in render arrays(在渲染数组里添加资源)
Libraries, JavaScript settings, feeds, HTML <head> tags and HTML <head> links等使用`#attached`属性来添加到渲染元素中。
`#attached`属性是一个关联数组，这些键是附件类型，值是附件数据。

`#attached`属性也可以用来指定HTTP头信息和响应码信息.

`#attached`属性允许加载资源库(含CSS资源，Javascript资源,Javascript设置, `feeds, HTML <head> tags，HTML <head> links`)，指定一个`type => value`的键值对，
这里的`type`大多是`library`, `drupalSettings`-Javascript设置。 例如:
```$xslt
$build['#attached']['library'][] = 'core/jquery';
$build['#attached']['drupalSettings']['foo'] = 'bar';
$build['#attached']['feed'][] = [
  $url,
  $this
    ->t('Feed title'),
];
```

可以查看`\Drupal\Core\Render\AttachmentsResponseProcessorInterface`,

关于如何定义资源库(libraries) `\Drupal\Core\Asset\LibraryDiscoveryParser::parseLibraryInfo()`

## Placeholders in render arrays(渲染数组中的占位符)
渲染数组有一个占位符机制，可以在渲染过程中再添加相应的数据到渲染数据中。
这与`\Drupal\Component\Render\FormattableMarkup::placeholderFormat()`的工作方式类似，
在渲染过程结束时，文本最终位于元素的标记(#markup)属性中，从存储在`#attached`属性的`placeholders`元素中的占位符获取替换。

例如: 渲染过程完成后，渲染数组表现如下:
```$xslt
$build['my_element'] = [
  '#attached' => [
    'placeholders' => [
      '@foo' => 'replacement',
    ],
  ],
  '#markup' => [
    'Something about @foo',
  ],
];
```
`#markup`最终将会是`Something about replacement`.

请注意，每个占位符值本身可以是一个将被渲染的数组，并且在渲染期间生成的任何缓存标记都将添加到标记的缓存标记中。

## The render pipeline(渲染管道)
术语“渲染管道”是指Drupal用于获取模块提供的信息并将其呈现为响应的过程。有关此过程的详细信息，请参阅https://www.drupal.org/developing/api/8/render。有关路由概念的背景，请参阅[路由API](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Routing%21routing.api.php/group/routing/8.6.x)。

实际上有多个渲染管道：
* Drupal总是使用symfony渲染管道。请参阅http://symfony.com/doc/2.7/components/http_kernel/introduction.html
* 在symfony渲染管道中，有一个Drupal渲染管道，它处理控制器返回的渲染数组。（Symfony的渲染管道只知道如何处理响应对象；此管道将渲染数组转换为响应对象。）
这些渲染数组被视为主要内容，可以渲染为多种格式：HTML、Ajax、对话框和模式对话框。模块可以通过实现一个主要内容渲染器来添加对更多格式的支持，
这个被标记为“render.main_content_renderer”的服务。
* 最后，在HTML主内容渲染器中，还有另一个管道，允许以多种方式渲染包含主内容的页面：完全没有装饰（仅显示主内容的页面）或区块（带有区域的页面，区块位于主内容周围的区域中）。
模块可以通过实现一个页面变量来提供额外的选项，该变量是一个使用`\Drupal\Core\Display\Annotation\PageDisplayVariant`注释的插件。

路由中控制器返回` \Symfony\Component\HttpFoundation\Response`的对象完全由symfony渲染管道处理。

路由中控制器将“主要内容”作为渲染数组返回并支持以多种格式（HTML、JSON等）和/或“修饰”的方式请求，如上所述。

## 相关文档
* core.libraries.yml
* hook_theme()
* Theme system overview
* \Symfony\Component\HttpKernel\KernelEvents::VIEW
* \Drupal\Core\EventSubscriber\MainContentViewSubscriber
* \Drupal\Core\Render\MainContent\MainContentRendererInterface
* \Drupal\Core\Render\MainContent\HtmlRenderer
* \Drupal\Core\Render\RenderEvents::SELECT_PAGE_DISPLAY_VARIANT
* \Drupal\Core\Render\Plugin\DisplayVariant\SimplePageVariant
* \Drupal\block\Plugin\DisplayVariant\BlockPageVariant
* \Drupal\Core\Render\BareHtmlPageRenderer

## File
core/lib/Drupal/Core/Render/theme.api.php, line 210

Hooks and documentation related to the theme and render system.
