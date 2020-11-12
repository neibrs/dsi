# 快速开始

## 介绍

Drupal GraphQL模块允许您使用正式的graphql查询语言查询或改变（更新/删除）任何内容或配置。这是一个非常强大的工具，它为Drupal在许多应用程序中的使用打开了大门。 


## 这个模块是为谁设计的？ 

任何想从Drupal获取JSON数据的人。

可以使用graphql模块的几个示例：

* 与Javascript前端（React、Angular、Ember等）分离的Drupal应用程序，
* Twig模板（Drupal主题）
* 需要永久性数据存储的移动应用程序
* 物联网数据存储 
## Hello World (快速启动)

1. 熟悉graphql语言。官方的graphql文档写得很好。http://graphql.org/learn/ 
2. 安装模块并启用Graphql和Graphql核心（机器名分别为“graphql”和“graphql-core”）. 
3. 登录并转到`/graphql/explorer` 
(Configuration > Web Services > GraphQL > Schemas > Explorer)
本地址是graphiql资源管理器。

4. **阅读评论** 然后在左窗格中输入以下查询: 

     ```graphql
     query {
       user: currentUserContext{
         ...on User {
           name
         }
       }
     }
     ```

5. Press `Ctrl-Space` 在右窗格中应该可以看到如下的内容 : 
    
    ```json
    {
      "data": {
        "user": {
            "name": "admin"
      }
    }
    ```

6. 恭喜！您刚刚了解了如何执行第一个graphql查询。此查询显示当前登录的用户。


**提示:**
* 随模块提供的graphiql explorer界面非常实用。您很可能会使用graphiql资源管理器来构建和测试更复杂的查询。
* graphql是自省的，这意味着整个模式（数据模型）都是预先知道的。这很重要，因为它允许像graphiql这样的工具实现自动完成。
* 一旦了解了基本的graphql语法，就可以使用graphql浏览数据和配置。您可以像在现代IDES中使用自动完成或智能感知那样在资源管理器中使用tab键。
* 上面的 `... User`提供了用户的所有字段,这个方法相当有用。


**资源**
* https://github.com/drupal-graphql/graphql
* https://drupal.slack.com - The GraphQL Slack Channel 非常活跃.
