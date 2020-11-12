Hook(钩子)
========
钩子即定义改变另一模块行为的一种函数。
模块改变另一个模块的核心行为的一种方法就是使用钩子。 钩子是模块定义的特殊命名函数（这称为“实现钩子”），
它们在特定时间被发现和调用以改变其行为或数据（这称为“调用钩子”））。 
每个钩子都有一个名称（例如：[hook_batch_alter（）](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Form%21form.api.php/function/hook_batch_alter/8.6.x)），
一组参数及一个返回值。 您的模块可以实现由平台核心或与其交互的其他模块定义的钩子。 
您的模块也可以定义自己的钩子，以便让其他模块与它们交互。
## 实现一个钩子：
* 找到钩子的文档，在* .api.php文件中，名称以“hook_”开头的函数（这些文件及其函数不会被Drupal加载，它们仅用于文档）。
该函数应该有一个文档头，以及一个示例函数体。例如，在核心文件[system.api.php](https://api.drupal.org/api/drupal/core%21modules%21system%21system.api.php/8.6.x)中，
您可以找到诸如hook_batch_alter（）之类的钩子。此外，如果您在API参考站点上查看此文档，则此主题中将列出Core(核心)钩子。
* 将该函数复制到模块的.module文件中。
* 更改函数的名称，例如，要实现hook_batch_alter（），您可以将其重命名为my_module_batch_alter（）。
* 编辑该函数的注解文档（通常注解为“Implements hook_batch_alter（）for xxx。”）。
* 编辑函数的主体，替换您模块需要执行的操作。
## 要定义一个钩子：
* 为您的钩子选择一个唯一的名称。它应该以“hook_”开头，然后是模块的名称。
* 在模块主目录的* .api.php文件中提供文档。应包含有关的内容（参数，返回值和样本函数体）的详细信息，请参阅核心已有钩子实例。
* 在模块的代码中调用钩子。
要调用钩子，请使用[\Drupal\Core\Extension\ModuleHandlerInterface](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Extension%21ModuleHandlerInterface.php/interface/ModuleHandlerInterface/8.6.x)上的方法，
例如alter（），invoke（）和invokeAll（）。您可以通过调用[\Drupal::moduleHandler（）](https://api.drupal.org/api/drupal/core%21lib%21Drupal.php/function/Drupal%3A%3AmoduleHandler/8.6.x)获取模块处理程序，
或者在注入的容器上获取​​“module_handler”服务。

