# HuiCMF 2.0 后台管理系统

> 基于[Webman](https://github.com/webman-php/webman)开发

### 演示地址：

[https://webman2.xiaohuihui.club/app/admin](https://webman2.xiaohuihui.club/app/admin)

账号：admin

密码：123456

### 功能模块

1. 首次默认自定义化安装
2. 增加应用插件管理，可安装、更新插件
3. 后台admin模块完全独立化，插件安装、**升级**
4. 后台权限管理，可管理子级权限
5. 支持自定义项配置
6. 支持字典配置管理
7. 附件管理
8. 后台登录日志、操作日志
9. 更多插件正在开发中，期待您的参与
10. ......

### 环境要求

1. php 8.0+ (推荐8.0)
2. mysql 5.6+ (推荐5.7)
3. 支持redis（默认缓存本地文件，配置redis需要开启redis服务）

---

## 安装：

### 1、clone拉取代码

```
 git clone https://gitee.com/xianrenqh/huicmf_webman.git
```

### 2、安装依赖包

> 如果使用composer安装依赖包执行报错，一般是禁用了函数，例如：putenv、proc_open、pcntl_fork等。放开即可。
>
> 一定要处理这个函数，否则无法启动或者安装部分composer包

```
 composer install
```

### 3、配置运行环境（宝塔面板为例）

#### 配置Nginx反向代理

官方案例：

```
upstream webman {
    server 127.0.0.1:8789;
    keepalive 10240;
}

server {
  server_name 站点域名;
  listen 80;
  access_log off;
  root /your/webman/public;

  location / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_set_header X-Forwarded-Proto $scheme;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```

自己整理案例：

```
upstream webman {
    server 127.0.0.1:8789;
    keepalive 10240;
}

server
{
    listen 80;
    listen [::]:80;
    server_name webman.xiaohuihui.net;
    index index.php index.html index.htm default.php default.htm default.html;
    root /www/wwwroot/webman.xiaohuihui.net/public;
    access_log off;

    #SSL-START SSL相关配置，请勿删除或修改下一行带注释的404规则
    #error_page 404/404.html;
    ssl_protocols TLSv1.1 TLSv1.2 TLSv1.3;
    ssl_ciphers EECDH+CHACHA20:EECDH+CHACHA20-draft:EECDH+AES128:RSA+AES128:EECDH+AES256:RSA+AES256:EECDH+3DES:RSA+3DES:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    add_header Strict-Transport-Security "max-age=31536000";
    error_page 497  https://$host$request_uri;

    #SSL-END

    #ERROR-PAGE-START  错误页配置，可以注释、删除或修改
    #error_page 404 /404.html;
    #error_page 502 /502.html;
    #ERROR-PAGE-END

    #PHP-INFO-START  PHP引用配置，可以注释或修改
    include enable-php-74.conf;
    #PHP-INFO-END

    #REWRITE-START URL重写规则引用,修改后将导致面板设置的伪静态规则失效
    include /www/server/panel/vhost/rewrite/webman.xiaohuihui.net.conf;
    #REWRITE-END

    #禁止访问的文件或目录
    location ~ ^/(\.user.ini|\.htaccess|\.git|\.svn|\.project|LICENSE|README.md)
    {
        return 404;
    }

    #一键申请SSL证书验证目录相关设置
    location ~ \.well-known{
        allow all;
    }

    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        expires      30d;
         # 这里是重点
        proxy_pass http://webman;
        error_log /dev/null;
        access_log /dev/null;
    }

    location ~ .*\.(js|css)?$
    {
        expires      12h;
        # 这里是重点
        proxy_pass http://webman;
        error_log /dev/null;
        access_log /dev/null;
    }


}
```

以上代码中，

**_location ~_** 中的

一定要注意，如果没处理好这里， 可能会出现部分静态资源无法访问到情况

#### 配置运行目录：

配置网站的运行目录为：**public**

#### 配置伪静态

```
location / {
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header Host $host;
    proxy_http_version 1.1;
    proxy_set_header Connection "";
    if (!-f $request_filename){
        proxy_pass http://webman;
    }
}
```

#### 修改端口（如需要）

```
/config/server.php文件
大概第16行：
'listen' => 'http://0.0.0.0:8789',
```

### 后台UI

1. 默认使用单页面（page）非iframe开发
2. 后台UI默认使用Pear Admin Layui 4.0版本
3. Layui默认使用最新版：2.9.10版本

---

### 执行启动命令

#### windows开发环境

```
php windows.php start
```

#### Linux生产环境

```
php start.php start
php start.php start -d
```

#### 平滑重启

```
php start.php reload
```

## 首次访问（安装）

> 注意配置的端口号

http:// 127.0.0.1:8789/app/admin

如果绑定域名请访问：

https://webman2.xiaohuihui.club/app/admin

域名更好成你自己的哦

---

## 后台开发说明

### 1、模板

> 注意：
>
> 在ui模板中：
>
>**不要出现（使用）相同的id**
>
> **不要出现（使用）相同的id**
>
> **不要出现（使用）相同的id**
>
>不要使用window.****=function()；
>
> 更换数据库配置，尤其是数据库名，一定要重启webman服务。

1、在页面中使用，一定要区分id值，不能和其他页面有重复的id值

**不要出现（使用）相同的id**

**不要出现（使用）相同的id**

**不要出现（使用）相同的id**

例如数据表格页：

```html

<table class="layui-hide" id="ruleTable" lay-filter="ruleTable"></table>
```

```javascript
var ruleInit = treeTable.render({
  elem: '#ruleTable',

});
```

```html

<script type="text/html" id="optionTpl">
    <a class="layui-btn layui-btn-xs layui-btn-normal" data-open="/app/admin/rule/add?id={{d.id}}"
       data-title="添加下级" data-reload="1" permission="app.admin.rule.add">添加下级</a>
    <a class="layui-btn layui-btn-xs layui-btn-success" data-open="/app/admin/rule/edit?id={{d.id}}"
       data-title="编辑" data-reload="1">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" data-confirm="/app/admin/rule/delete"
       data-data="id={{d.id}}" data-title="确定要删除吗？" data-reload="1">删除</a>
</script>
```

```javascript
[
  {fixed: "right", title: "操作", width: 190, align: "center", toolbar: "#optionTpl"}
]
```

以上代码中：js中的 `elem`值和html中的`id`值一致，且其值不能和其他页面中有相同的id值，否则页面无法展示会显示空白。
最好的命名规则为：当前规则名称+Table，例如上面的：ruleTable，意为：角色表格。

### 数据表格（layui）拖拽排序方法：

> 参考：https://www.workerman.net/a/1677

#### html页面：

引入soulTable：

```javascript
layui.use(['table','soulTable'], function () {
    var soulTable = layui.soulTable;
    table.render({
        rowDrag: {
        trigger: ".layui-icon-snowflake",
        done: (obj) => {
            let PRIMARY_KEY = 'id'; //排序字段id
            let UPDATE_API = '/app/knowledge/knowledge/sort';   //请求排序接口API
            rowDragDoneFunc(obj, PRIMARY_KEY, UPDATE_API, 'sort');
            refreshTable(tableInit.config.id, 1);   //刷新表格
            },
        },
        done: function () {
        soulTable.render(this)
        }
    })
})
```

#### 后台控制器处理：

```php
//引入排序
use plugin\admin\app\servicers\DragdoneUpService;

/**
     * 排序
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sort(Request $request): Response
    {
        if ($request->method() === 'POST') {
            if ($request->post('dragDone') == 1 && DragdoneUpService::dragDoneUpData($request, $this->model)) {
                return $this->success('操作成功');
            }
        }
    }
```

### wangEditor上传视频方法案例：

```javascript
  window.editor = E.createEditor({
  uploadVideo: {
  server: '/app/admin/upload/upload?editor_type=wang',
  fieldName: 'custom-fileName',
  meta: {token: 'xxx', a: 100},
  metaWithUrl: true, // join params to url
  headers:
{
  Accept: 'text/x-json'
}
  ,
  maxFileSize: 10 * 1024 * 1024, // 10M
  onBeforeUpload(file) {
  return file
}
  ,
  onProgress(progress) {
  console.log('progress', progress)
}
}
})

```

## 其他

后台插件如不想升级，请在对应的插件配置文件，例如：admin `plugin/admin/config/app.php` 中将 `version` 改为 任意最大值。例如：10.0.0
