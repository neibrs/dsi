开发工具
=======

常用开发工具：
* `git` 代码版本管理
* `PhpStorm` 继承开发环境
* `Docker` 开发/生产环境
* `screen` 多终端管理
* `gitk` 图形化代码版本管理
* `tig` 服务器代码版本查看

## git常用命令

* `git status` 查看当前版本状态
* `git add 文件或目录` 添加要提交的文件
* `git commit -am '说明'` 提交代码
* `git pull` 更新本地代码
* `git checkout 分支` 切换到分支
* `git branch -a` 查看分支列表
* `git merge 版本号` 合并分支
* `git branch -d 分支`    删除本地分支
* `git push origin --delete 分支` 删除远程分支

技巧：配置系统PS1显示版本号:
```bash
PS1='${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]$(__git_ps1 " \[\033[01;32m\]%s\[\033[00m\]")\$ '
```

代码统计:
git log --pretty='%aN' --since="2020-08-24" | sort | uniq -c | sort -k1 -n -r

## 解决方法: PhpStorm打断点后运行代码未中断

为了性能，系统提供了大量缓存。加载界面时，很多代码是不会被运行的。解决方法是运行命令清除缓存：
```
vender/bin/drupal cr all
```
用命令清除缓存后还是未能中断，这是因为有些代码是在清除缓存时运行的。解决方法是通过界面清除缓存，URL地址为：
```
admin/config/development/performance
```

## screen使用技巧

在~/.screenrc配置screen:
```
defscrollback 10000
caption always "%n(%t): %C"
```

常用命令：
* `screen -ls` 查看可用的screen窗口
* `screen -d 窗口ID` 释放该窗口供使用
* `screen -r 窗口ID` 进入该窗口

快捷键:
* `CTRL+a+c` 创建窗口
* `CTRL+a+0...9` 切换窗口
* `CTRL+a+CTRL+[` 翻屏查看历史信息
* `CTRL+a+A` 修改窗口标题
