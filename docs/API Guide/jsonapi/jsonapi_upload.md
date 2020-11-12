JSON API 上传文件
=============

本地文件将可以使用JSON:API模块来上传了，

目前你可以按照这个issue进行功能更新[#2958554: Allow creation of file entities from binary data via JSON API requests](https://www.drupal.org/project/jsonapi/issues/2958554).

与以前对json:api的文件上传支持不同，您将不再需要首先对文件进行base64编码。相反，您可以直接将原始文件数据发送到Drupal。这意味着你可以上传更大的文件，并且做得更快！