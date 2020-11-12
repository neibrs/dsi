使用PATCH方法更新内容实体的数据
========================

使用下面的两步来暴露REST资源给GET请求:
1. Configuration
2. 测试Patch请求

1. Connfiguration
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
```

## 测试PATCH请求
就像post示例中所要求的那样，这个PATCH示例也需要有链接，因为我们使用的是hal+json。
cURL (command line)
```
curl --include \
  --request PATCH \
  --user klausi:secret \
  --header 'Content-type: application/hal+json' \
  --header 'X-CSRF-Token: <obtained from http://example.com/rest/session/token>' \
  http://example.com/node/56?_format=hal_json \
  --data-binary '{"_links":{"type":{"href":"http://example.com/rest/type/node/article"}},"title":[{"value":"Example node title UPDATED!"}],"type":[{"target_id":"article"}]}'
```
Guzzle
```
<?php
$serialized_entity = json_encode([
  'title' => [['value' => 'Example node title UPDATED']],
  'type' => [['target_id' => 'article']],
  '_links' => ['type' => [
      'href' => 'http://example.com/rest/type/node/article'
  ]],
]);

$response = \Drupal::httpClient()
  ->patch('http://example.com/node/56?_format=hal_json', [
    'auth' => ['klausi', 'secret'],
    'body' => $serialized_entity,
    'headers' => [
      'Content-Type' => 'application/hal+json',
      'X-CSRF-Token' => <obtained from /rest/session/token>
    ],
  ]);
?>
```

jQuery
```
function getCsrfToken(callback) {
  jQuery
    .get(Drupal.url('rest/session/token'))
    .done(function (data) {
      var csrfToken = data;
      callback(csrfToken);
    });
}

function patchNode(csrfToken, node) {
  jQuery.ajax({
    url: 'http://example.com/node/56?_format=hal_json',
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/hal+json',
      'X-CSRF-Token': csrfToken
    },
    data: JSON.stringify(node),
    success: function (node) {
      console.log(node);
    }
  });
}

var newNode = {
  _links: {
    type: {
      href: 'http://example.com/rest/type/node/article'
    }
  },
  type: {
    target_id: 'article'
  },
  title: {
    value: 'Example node title UPDATED'
  }
};

getCsrfToken(function (csrfToken) {
  patchNode(csrfToken, newNode);
});
```

PATCH术语

```
curl --request PATCH -k -i -s --user user:password --header 'Content-type: application/hal+json' -H 'Cache-Control: no-cache' --header 'X-CSRF-Token: <obtained from http://example.com/rest/session/token>' 'http://example.com/node/2081?_format=hal_json' --data-binary '
{
  "_links": {
      "self": {
        "href": "http://example.com/node/2081?_format=hal_json"
      },
    "type": {
      "href": "http://example.com/rest/type/node/article"
    },
    "http://example.com/rest/relation/node/article/field_tags": {
       "href": "http://example.com/taxonomy/term/1?_format=hal_json"
    }
  },
  "type": {
      "target_id": "article"
    },
    "nid": [
      {
        "value": "2081"
      }
    ],
  "title": {
      "value": "My Article PATCHED"
    },
  "body": {
      "value": "PATCHED"
  },
    "_embedded": {
      "http://example.com/rest/relation/node/article/field_tags": [
        {
          "_links": {
            "self": {
              "href": "http://example.com/taxonomy/term/1?_format=hal_json"
            },
            "type": {
              "href": "http://example.com/rest/type/taxonomy_term/tags"
            }
          },
          "uuid": [
            {
              "value": "ff61ea71-2540-47fe-a4bb-384b12d4de47"
            }
          ],
          "lang": "en"
        }
      ]
    }
}'
```