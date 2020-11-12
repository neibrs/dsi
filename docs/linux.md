Linux基本命令
============

列出当前目录所有文件:
```bash
ls
```
进入指定目录:
```bash
cd 目录路径
```
新建文件夹:
```bash
mkdir 目录名称
```
搜索当前目录及下级目录的所有文件，搜索指定的字符串:
```bash
grep 要搜索的字符串 * -r
```
查找文件名符合制定模式的文件:
```bash
find -name '*Controller.php'
```
查找文件路径符合指定模式的文件:
```
find -path '*/Plugin/views/filter/*
```
全文替换，将xxx替换为yyy:
```bash
sed -i 's/xxx/yyy/' `grep 'xxx' . -rl`
```
进入远程服务器:
```bash
ssh 用户@IP地址
```

## 其他

* `cat 文件名 | sed '2d` 删除第2行
* `cat 文件名 | sed '1,2d` 删除前两行
* `cat 文件名 | awk '{print $9}'` 打印第9列

## 磁盘空间管理

* `df -h` 查看硬盘使用情况
* `sudo du -a /var | sort -n -r | head -n 10` 占用磁盘空间最大的前10个文件或文件夹
