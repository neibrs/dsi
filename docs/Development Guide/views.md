Views(视图)
==========

Views模块是一个通用查询和显示引擎，可用于创建数据项（通常是实体，也可以是其他类型的数据）的视图（格式化列表，网格，提要和其他输出）。
开发人员可以通过以下几种方式操作views：
* **提供插件**：视图模块提供的插件几乎管理视图的每个方面，包括查询（排序，过滤等）和显示（在几个粒度级别，从整个视图到字段的详细信息）,如用于过滤查询的combine组合搜索插件，
用于显示列表里字段的field插件，用于批量操作的bulk_form插件等
* **提供数据**：通过实现hook_views_data（）可以向Views提供数据类型，并且可以通过实现hook_views_data_alter（）来更改其他模块提供的数据类型。
要为实体提供视图数据的话，需创建一个实现\Drupal\views\EntityViewsDataInterface的类，并在实体类的“views_data”注释中引用它。如果扩展\Drupal\views\EntityViewsData基类，则可以自动生成继成的大部分内容。
* **实现hooks(钩子)**：视图中的一些操作可受到钩子的影响。参考views.api.php
* **Theming(主题)**：TODO
