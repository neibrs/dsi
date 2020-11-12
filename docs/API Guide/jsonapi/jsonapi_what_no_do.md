JSONAPI 不能做什么
==================

JSON:API完全基于实体。那是因为它不能处理任何关于实体CRUD的操作。像注册账号，登录账号，请求新密码等业务规则并不是JSON:API的支持功能。这些大多功能都是由Drupal核心提供的。

下面列出了常见需求和解决方案的非详尽列表。
路径列表:
* /session/token
* /user/register
* /user/login
* /user/login_status
* /user/logout

### 获取会话token
#### 获取token
```
curl --request GET http://localhost:8181/session/token
```
#### 使用token
除了登录时的会话token，您还可以获得CSRF_token和logout_token。
#### 用户注册
```
curl \
  --header "Content-type: application/json" \
  -c cookie.txt \
  --request POST "http://localhost:8181/user/login?_format=json" \
  --data '{"name": "admin", "pass": "admin"}'
```
`-c cookie.txt`告诉curl要保存一个cookie。你的响应信息应该像这样:
```
{
	"current_user": {
		"uid": "1",
		"roles": ["authenticated", "administrator", "implementor", "system_administrator", "employee", "price_manager"],
		"name": "admin"
	},
	"csrf_token": "dwwHtZxQdcms8-2Mjhng515LcVr7UK0MSJUY6t8uvs4",
	"logout_token": "gxmuOv6mfVTzOo9Jur-mHFKXNxWUROkVidw1_3GkZ7c"
}
```
#### 用户状态
```
curl \
  --header "Content-type: application/json" \
  -b cookie.txt \
  --request GET "http://localhost:8181/user/login_status?_format=json"
```
`-b cookie.txt` 告诉curl发送(不是保存)cookie, 如果登录成功，则会返回1。

#### 退出登录(TODO)
```
curl \
  --header "Content-type: application/json" \
  -b cookie.txt \
  --request POST "http://localhost:8181/user/logout?_format=json"
```
这里退出登录用户。
#### 授权机制
上面的示例只是许多可用的身份验证机制之一。您应该探索哪种机制最适合您的需求。
Drupal 8 [simple_oauth](https://www.drupal.org/project/simple_oauth)模块也提供了简单认证功能。

参考
查看https://www.drupal.org/node/2720655
(login, login_status and logout)和https://www.drupal.org/node/2752071 (register)以获取更多信息.
也可以参考[使用其他授权协议](https://www.drupal.org/docs/8/core/modules/rest/using-other-authentication-protocols)
## JSON:API其他内容
使用JSON:API额外内容来修改JSON:API配置。

当需要更改资源属性（如API路径）、禁用资源、别名字段等时，请使用[JSON:API Extras](https://www.drupal.org/project/jsonapi_extras).

启用模块后，您可以访问/admin/config/services/jsonapi查看JSON:API公开的所有配置和内容的列表。
