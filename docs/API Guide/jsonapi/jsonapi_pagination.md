JSON API 分页
===========

分页可能是一个复杂的主题。很容易掉入陷阱，不遵循最佳实践。
本页将帮助您进行“正确”的分页。也就是说，如果你阅读并理解这一页，
我们认为你的客户将更加强大.

TODO 如果只从本指南中去掉一件事，则不应构建自己的分页URL。

json:api模块的每个分页响应都已经有指向集合下一页的链接供您使用。你应该遵循这个链接。

在本文的开头，我们将介绍API的一些重要特性以及如何正确实现分页。在本文档的最后，您将找到一些常见问题的答案。

## 怎么做?
每个分页响应都有内置的分页链接。让我们来看一个小例子：
```
{
  "data": [
    {"type": "sample--type", "id": "abcd-uuid-here"},
    {"type": "sample--type", "id": "efgh-uuid-here"}
  ],
  "links": {
    "self": "<collection_url>?page[offset]=1&page[limit]=3",
    "next": "<collection_url>?page[offset]=2&page[limit]=3",
    "prev": "<collection_url>?page[offset]=0&page[limit]=3"
  }
}
```

* links键下有3个分页链接:
  * **self**: this is the URL for the current page.
  * **next**: this is the URL for the next page.
  * **previous**: this is the URL for the previous page.
* `page[limit]`为3， 但是总共就2个资源.

分页链接的存在或不存在是很重要的。(The presence or absence of the pagination links is significant. )
1. 如果`next`链接存在，就一定会有更多的页
2. 如果`next`链接不存在, 一定是在最后一页
3. 如果`prev`链接不存在，一定不是在第一页
4. 如果`next`,`prev`都不存在，那么仅有一页数据

即使每页限制为3条，也仅只有2个资源！这是因为出于安全原因删除了实体。我们可以说这不是因为没有足够的资源来填充响应，

因为我们可以看到有下一个链接。如果您想了解更多信息，下面将详细介绍。

好吧，既然我们已经确定了一些重要的事实。让我们想想如何建立我们的客户。我们将查看一些伪JavaScript来帮助您。

假设您想在我们的网站上显示最新内容的列表，我们有一些“高级”内容。

只有付费用户才可以看到付费内容。我们还决定要一个“前5”组件，

但是，如果存在更多的内容，用户应该能够单击“下一页”链接来查看下5个最新的内容。

一个简单的实现可能看起来像这样：
```
const baseUrl = 'http://example.com';
const path = '/jsonapi/node/content';
const pager = 'page[limit]=5';
const filter = `filter[field_premium][value]=${user.isSubscriber()}`;

fetch(`${baseUrl}${path}?${pager}&${filter}`)
  .then(resp => {
    return resp.ok ? resp.json() : Promise.reject(resp.statusText);
  })
  .then(document => listComponent.setContent(document.data))
  .catch(console.log);
```
然而，即使忽略了可怕的错误处理，我们已经知道这不是一个非常健壮的实现。

我们已经看到，我们不能确定一个响应将有5个项目。如果其中2个实体无法访问（可能未发布），那么我们的“前5”组件将只有3个项目！

我们还有一个不必要的过滤器。服务器应该已经删除了用户不允许看到的内容。

否则，我们的应用程序中可能会绕过访问，因为恶意用户可以轻松地更改查询以查看“高级”内容。

始终确保在服务器上实施访问控制；不要信任您的查询来为您执行访问控制。

修改后：
```
const listQuota = 5;
const content = [];
const baseUrl = 'http://example.com';
const path = '/jsonapi/node/content';
const pager = `page[limit]=${listQuota}`;

const getAndSetContent = (link) => {
  fetch(link)
  .then(resp => {
    return resp.ok ? resp.json() : Promise.reject(resp.statusText);
  })
  .then(document => {
    content.push(...document.data);
    listContent.setContent(content.slice(0, listQuota));

    const hasNextPage = document.links.hasOwnProperty("next");
    if (content.length <= listQuota && hasNextPage) {
      getAndSetContent(document.links.next);
    }

    if (content.length > listQuota || hasNextPage) {
      const nextPageLink = hasNextPage
        ? document.links.next
        : null;
      listComponent.showNextPageLink(nextPageLink);
    }
  })
  .catch(console.log);
}

getAndSetContent(`${baseUrl}${path}?${pager}`)
```
首先，您可以看到过滤器不见了。这是因为我们假设访问检查是在服务器上执行的，

而不是依赖于过滤器。这是唯一安全的解决方案。我们可以将其作为性能优化添加回去，但这可能不是必需的。

接下来，因为我们知道服务器只是删除用户无法访问的资源，所以我们需要检查响应中实际有多少资源。

在“Native”的实现中，我们假设每个响应都有5个项目。

在这个例子中，我们现在设置了5个资源的“配额”。在我们提出请求后，我们检查一下是否达到了我们的配额。

我们还会检查服务器是否还有更多的页面（我们会知道这一点，因为它会有下一个链接，记得吗？）.

如果我们还没有达到限额，而且还没有到达最后一页，我们将使用从文档中提取的下一个链接提出另一个请求。

需要注意的是，我们没有手工为下一页构建新的URL。不需要重新设计轮子，因为json:api服务器已经为我们做了！

另一个需要注意的有趣的事情是，由于fetch是异步的，所以我们可以在发出所有请求之前将第一个请求的内容添加到组件中。

当第二个请求完成时，我们只是再次更新组件，以便它包含新获取的结果。

最后，我们要确保虚构的`listComponent`知道是否显示“下一页”链接。

只有当我们已经有额外的内容或者服务器有额外的页面时，它才应该显示链接。

如果我们在第一个请求中只接收到4个项目，在第二个请求中只接收到5个项目，

但没有下一个链接，则可能会发生前一种情况。在这种情况下，我们总共有9个项目，

但`listComponent`只显示前5个。因此，我们仍然希望在组件上显示“下一页”链接，

但不希望组件实际发送更多请求。为了表明这一点，我们将`nextPagelink`设置为 `null`。

在后一种情况下，当我们有`下一个`链接时，我们将下一个页面链接传递给我们的组件，以便它可以使用它来发出后续请求。

如果用户从不单击“下一页”链接，我们不想发出请求，是吗？

最后几段说明了一个非常重要的概念……”下一页“ HTML中的链接不需要与API页面关联！事实上，这是一个迹象，表明如果你做的是“错误的”的话。


## Why ... ?
... **不能查看页面总数？**

新来者经常因为看不到总页面或资源计数而被绊倒。这是一个合理的期望。但是，json:api模块不提供计数，因为它会严重降低性能。

假设一个应用程序有200000个实体，分页限制为50，这似乎表明应该有4000页（每页200000个实体/50个实体）。对吗？

不幸的是，这并不能保证是真的！json:api模块必须检查对它放入响应中的每个资源的访问。

如果这些实体中只有50个不可访问（可能未发布），那么页数将为3999。

也许这种不准确似乎是可以容忍的，但是如果一半的实体是不可访问的呢？如果它们都无法访问呢？当用户没有权限查看该类型的实体时，这可能是真的。

json:api模块还需要考虑包含总计数的性能影响。当应用程序每页最多只能包含50个实体时，您真的希望它运行200000个实体访问检查吗？

由于所有这些考虑，json:api模块不包括总页数。


... **我不能将页面限制设置为大于50吗？**

首先，阅读上面给出的示例。了解json:api必须对响应中的每个实体运行单独的访问检查。

其次，了解json:api模块的目标是“零配置”，您不必安装、更改或配置任何东西来使用该模块。

这样做的原因是为了保护您的应用程序免受DDOS攻击。如果恶意API客户机将页面限制为200000个资源，

json:api模块将需要对这些实体中的每个实体运行实体访问检查。这将很快导致内存不足错误和响应缓慢。

服务器需要设置最大值。50的限制是任意选择的一个很好的整数。

请理解，围绕这一决定进行了许多长时间的讨论，必须在支持负担、理智的违约和前端性能之间作出妥协。

虽然json:api模块维护人员确实理解，这可能不适合每个用例，但他们相信，如果您的客户机遵循这些文档中的建议，它对您的影响应该很小，甚至没有影响：）

... **响应中是否有x个资源？**

json:api模块允许您指定一个页面限制，这通常被误解为保证响应中包含一定数量的资源。

例如，您可能知道有足够的资源可用于“填充”响应，但响应没有您所期望的那么多资源。

出于上述许多相同的原因，json:api只对page[limit]查询参数指定的项目数运行数据库查询。

这只是一个最大值。如果不允许访问查询结果中的某些资源，则将从响应中删除这些资源。在这种情况下，您将看到比预期更少的资源。

当请求可能未发布的实体（如Node）并且这些实体尚未使用筛选查询参数筛选出时，这是非常常见的。