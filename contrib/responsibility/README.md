responsibility
===============

## TODO

结论：
1. 为responsibility提供entity_permission设置
2. 对entity_permission模块加强：提供数据组(business_group...organization)设置

问题：如何判断控制数据组的字段？  解答：organization字段

## Old

职责：团队管理  实体：vacancy  权限: [...]  数据组：本机构

responsibility_permission
职责      实体              操作  数据组
团队管理  vacancy           查看  本业务组
团队管理  vacancy           添加  本机构
团队管理  extra_information 添加  本机构

responsibility permission plugin:
entity permission
bundle permission
field permission
workflow_transition
