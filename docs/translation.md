翻译
=====

一、翻译过程概览
1、 哪些需要使用翻译功能?

开发者使用t(),format_plural()以及其他一些相关多语言化的函数,用来多语言化所开发的
模块;这些APIs允许Drupal核心在显示给用户时,使用不同语言。

2、 从官网获取翻译文件

你可以使用localize来获取核心的相关翻译文件,但文件内容可能会比较冗长。因此,也
可以使用Localization update这个模块来自动安装或更新本地网站的翻译。

二、自己动手给Drupal汉化

1、翻译方式的说明
这里包含一个有助于你对Drupal汉化过程的基本指导方针;如果译者能够用词一致,就比
较容易理解Drupal。

2、PO and POT 文件的关系

Drupal使用 .po (翻译文件)和 .pot (翻译文件模板)作为翻译文件。都是使用GNU Gettext
format。最基本的不同是 .pot 文件里面不包含任何翻译字样,而 .po 文件里面是以 .pot
为基础的翻译文件。

POT文件
POT文件的来源;使用 Translation template extractor(potx) 这个翻译导出工具进行,
常用命令是
````yaml
vendor/bin/drush potx single --include=modules/contrib/potx/ --
folder="path/to/modules/mymodule/" --api=8
````

上面的mymodule替换成需要的模块名称即可。

PO文件

PO文件来源依赖于POT文件,在对应的模块里面把pot文件复制成 %module.zh-hans.po 即
可。
POT和PO文件里面的字符串分隔符
Drupal只支持单引号和双引号引用PO和POT文件里面的字符串。
示例
POT文件示例:
````
#: modules/user/views_handler_filter_user_name.inc:29
msgid "Enter a comma separated list of user names."
msgstr ""
#: modules/user/views_handler_filter_user_name.inc:112
msgid "Unable to find user: @users"
msgid_plural "Unable to find users: @users"
msgstr[0] ""
msgstr[1] ""
````

中文版的PO文件示例:
````
#: modules/user/views_handler_filter_user_name.inc:29
msgid "Enter a comma separated list of user names."
msgstr "Eine kommagetrennte Liste von Benutzernamen."
#: modules/user/views_handler_filter_user_name.inc:112
msgid "Unable to find user: @users"
msgid_plural "Unable to find users: @users"
msgstr[0] "找不不到用用户: @users"
msgstr[1] "找不不到用用户: @users"
````

在第二段中的@users是一个变量,并且会被用户接口中的数字所替换。只要是@, !, 和 %
开头的字样,或者被{},如{text}这样的,都是不会被翻译的。

3、如何生成待翻译的pot文件和po文件

````
vendor/bin/drush potx single --include=modules/contrib/potx/ --folder=modu
les/mymodule/ --api=8
````

后面的mymodule替换成需要翻译的目录模块。这个命令执行后,将在drupal的根目录里
面产生一个generator.pot文件,需要转移至你上面输入的一个模块里面的translations目录
下,并复制一个中文po文件并重命名为 %module.zh-hans.po ,然后在po文件里面进行翻
译。

4、常规导入单个翻译文件
````
http://[your_site]/admin/config/regional/translate/import (Drupal 8):
````

这个URI下执行单个模块的翻译文件导入。

5、如何把已翻译的文件全部导入本地网站中(新装模块异常状态下
执行全部翻译导入动作)

平台module/eabax/bin目录里面有一个langimp.sh文件,执行之间编辑好已经准备
的待翻译模块的po路径;执行后,即导入成功!

## 生成 pot 文件
如果系统未安装potx模块，需先安装：
```bash
vendor/bin/drupal moi potx
```
然后生成pot文件：
```bash
vendor/bin/drush potx single --include=modules/potx --folder="模块目录/" --api=8
cp general.pot 模块目录/transactions/模块名.pot
```

## 在`模块.info.yml`里配置po文件
```yml
interface translation project: 模块名
interface translation server pattern: 路径/%project/translations/%project.%language.po
```

## 翻译文件提交的版本
为了避免冲突：开发版添加的模块只能提交到开发版，测试版添加的模块只能提交到测试版，其他模块提交到稳定版。
