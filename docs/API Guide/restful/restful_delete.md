使用DELETE方法删除内容实体的数据
========================

使用下面的两步来删除REST资源:
1. Configuration
2. 测试Delete请求

## 1. Configuration
RESTful配置如下:
```
resources:
  entity:node:
    GET:
      supported_formats:
        - hal_json
      supported_auth:
        - basic_auth
        - cookie
    POST:
      supported_formats:
        - hal_json
      supported_auth:
        - basic_auth
        - cookie
    PATCH:
      supported_formats:
        - hal_json
      supported_auth:
        - basic_auth
        - cookie
    DELETE:
      supported_formats:
        - hal_json
      supported_auth:
        - basic_auth
        - cookie
```

**测试DELETE请求**
本例中，`Content-Type`不必要，因为没有请求内容发送。
####  cURL(命令行)
```$xslt
curl --include \
  --request DELETE \
  --user klausi:secret \
  --header 'X-CSRF-Token: <obtained from http://example.com/rest/session/token>' \
  http://example.com/node/56?_format=hal_json \
```

#### Guzzle
```
<?php
$response = \Drupal::httpClient()
  ->delete('http://example.com/node/56?_format=hal_json', [
    'auth' => ['klausi', 'secret'],
    'headers' => [
      'X-CSRF-Token' => <obtained from /rest/session/token>
    ],
  ]);
?>
```

#### jQuery
```
function getCsrfToken(callback) {
  jQuery
    .get(Drupal.url('rest/session/token'))
    .done(function (data) {
      var csrfToken = data;
      callback(csrfToken);
    });
}

function deleteNode(csrfToken) {
  jQuery.ajax({
    url: 'http://example.com/node/56?_format=hal_json',
    method: 'DELETE',
    headers: {
      'X-CSRF-Token': csrfToken
    },
    success: function () {
      console.log('Node deleted.');
    })
  });
}

getCsrfToken(function (csrfToken) {
  deleteNode(csrfToken);
});
```