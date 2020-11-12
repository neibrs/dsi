docker环境
=========

## 安装 docker-compose

* 安装docker-compose ,执行命令：sudo apt install docker-compose
* 将linux用户添加到 docker 组: sudo usermod -a -G docker 用户名
  然后需要重新登陆
* 配置 insecure-registries：执行命令 sudo vim /etc/docker/daemon.json ,然后在文件里添加如下配置
{
  "insecure-registries":["114.215.42.138:5000"]
}
* 重启 docker 服务器: sudo service docker restart

## 四大环境

环境          |语言 |类型     |性能   |调试       |其他
-------------|-----|--------|-------|----------|--------
eabax        |英文版|生产环境 |速度最快|不能中断调试|
eabax_dev    |英文版|开发环境 |       |可中断调试  |含phpmyadmin
eabax_zh     |中文版|生产环境 |速度快  |不能中断调试|
eabax_zh_dev |中文版|开发环境 |       |可中断调试  |含phpmyadmin,可生成pot文件

技巧：可在eabax运行稳定版，eabax_dev/eabax_zh运行测试版，eabaz_zh_dev运行开发版。

技巧：可通过screen开8个窗口，分别运行这四个环境的容器终端、外部代码终端。

docker容器启动后，浏览器可通过`localhost:端口地址`访问系统。

环境          |端口地址
-------------|--------
eabax         |8180
eabax_dev     |8181
eabax_zh      |8182
eabax_zh_dev  |8183

## 建立本地环境

第一步：通过 build.sh 将服务器的 docker 镜像拖到本地，并建立环境：
```bash
mkdir ~/src
cd ~/src
git clone ssh://git@114.215.42.138:/opt/git/eabax-docker.git
cd ~/src/eabax-docker/环境      # 环境可以是eabax,eabax_dev,eabax_zh,eabax_zh_dev
./build.sh
```

第二步：创建容器：
```bash
cd ~/src/eabax-docker/环境      # 环境可以是eabax,eabax_dev,eabax_zh,eabax_zh_dev
#docker-compose bulid
docker-compose up -d
docker run --name eabax_dev -it 114.215.42.138:5000/eabax_dev
docker run --name eabax_dev -d 114.215.42.138:5000/eabax_dev
```

第三步：进入容器安装系统：
```bash
docker exec -it 环境 bash
modules/dsi/bin/install-环境.sh
```

## Docker常用命令

* 启动Docker容器：
```bash
cd ~/src/eabax-docker/环境      # 环境可以是eabax,eabax_dev,eabax_zh,eabax_zh_dev
docker-compose start
```

如果要运行命令，需进入Docker容器命令终端:
```
docker exec -it 容器名 bash        # 容器名包括eabax,eabax_dev,eabax_zh,eabax_zh_dev
```
然后在此终端里可以进行一些操作 ：如查看日志：
````bash
tail -f /var/log/apache2/error.log
```` 
如安装模块，
````bash
vendor/bin/drush en -y 模块名
````
等等的一些操作。

## 其他:
安装docker: 执行命令
````bash
sudo apt install  docker
````


* 重启docker服务：执行命令 systemctl restart docker
* 登录我们的docker镜像服务器: 执行命令 sudo docker login 114.215.42.138:5000然后输入用户eabax,密码
* 拉取docker镜像: 执行命令 git clone ssh://git@114.215.42.138:/opt/git/eabax-docker.git
* 构建最新代码环境，进入到/eabax-docker/eabax_dev（开发环境）下，执行命令：sudo sh build.sh
* 启动 eabax_dev容器，执行命令：docker-compose up -d (注：执行此命令是在容器所在目录下)
* 启动成功后 浏览器输入地址：localhost:8181 ,能够访问成功
* 进入docker容器终端：执行命令 docker exec -it eabax_dev bash ,
* 安装phpstorm, 执行命令：sudo snap install phpstorm –classic, 安装完后 在命令行输入 phpstorm
    即可启动
    
## Docker 容器备份恢复
导出：
```bash
docker export 容器名 > 文件名.tar
```

导入：
```bash
docker import 文件名.tar
```

## 搭建和管理registry服务器

运行registry服务器：
```bash
mkdir ~/src/docker-registry
cd ~/src/docker-registry
mkdir auth
docker run --entrypoint htpasswd registry -Bbn 用户 密码 > auth/htpasswd

docker run -d -p 5000:5000 --restart=always --name registry_private -v `pwd`/auth:/auth -e "REGISTRY_AUTH=htpasswd" -e "REGISTRY_AUTH_HTPASSWD_REALM=Registry Realm" -e "REGISTRY_AUTH_HTPASSWD_PATH=/auth/htpasswd" registry
```

删除镜像：
```bash
curl -X DELETE localhost:5000/v2/镜像名/manifests/sha256:值
```

垃圾回收：
```bash
docker exec -it registry_private registry garbage-collect /etc/docker/registry/config.yml
```

清理早期push的无用镜像：
```bash
repositories="eabax eabax_dev eabax_zh eabax_zh_dev"
for repository in $repositories;
do 
  echo Processing $repository
  revisions=`sudo ls /var/lib/docker/volumes/6ef9eae32e449ea0a2b34eea27e604b88e5ab753aa1cc42efb9fde36fa2a8eb5/_data/docker/registry/v2/repositories/$repository/_manifests/revisions/sha256 -lt | sed '1,8d' | awk '{print $9}'`
  for revision in $revisions;
  do
    echo Delete $revision
    curl --user eabax:454bc7b1 -X DELETE localhost:5000/v2/$repository/manifests/sha256:$revision
  done
done

echo Running garbage collect
docker exec -it registry_private registry garbage-collect /etc/docker/registry/config.yml
```