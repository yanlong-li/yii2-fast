Yii2 快速开发扩展插件
=

> 本不想使用Yii2框架，奈何公司要求只能使用Yii2框架。于是乎此扩展出生。

## 前言

本扩展包是作者长期使用 `ThinkPHP` 和 `Laravel` 框架之下,养成的一些习惯。奈何Yii2框架不支持，于是工作过程中开发并一点点完善此扩展。

## 介绍


仿照 `ThinkPHP` 和 `Laravel` 框架编写此扩展，所以很多代码可能会直接由这两个框架中的代码直接复制过来，也有直接仿照源代码进行细微调整以适应Yii2框架。如果有问题欢迎来`Gitee`提交问题。
同时在项目内也会附带上两个框架的开源许可协议。

## 计划支持

    Request、Cookie、Session、Cache、Db
    
    以及支持表模型的多态关联、自动化的远程关联
    

`ps:具体支不支持请关注更新日志和操作说明`
## 已支持
|类名|用途|备注|
|---|---|---|
|Request|获取请求参数|支持json、xml内容类型|
|Config|获取配置参数|支持Param及新增文件，排除main、test等文件|
|Pagination|模型分页查询|需要传递ActiveRecord模型|
|Lang|语言快速渲染|基于Yii原有国际化语言支持|
|Cache|缓存读写、自增减|基于原始Yii文件缓存系统的缓存类|
|Controller|控制器扩展，视图渲染，参数绑定|render、assign|
|ActiveRecord|数据库模型|多态关联、远程关联等|

## 总结
 原框架设计接口包括兼容性和逻辑性一定是比次扩展好用，但是呢，Yii2框架本身也有一些封装，所以够用就行。
 
 因为我比较懒
 
## 更新日志
    
    2019年8月9日
    Config 修复主配置比-local.php级别高的问题
    
    2019年8月8日
    Config 排除非php文件
    
    2019年8月5日
    更换包名 
    yanlongli/yii2-fast
    安装旧包的可能需要移除后重新安装
    命名空间不变
    
    2019年8月5日
    修复Config 未初始化时 params未放置到数组中
    增加自动初始化类
    开放加载自定义配置文件
    
    2019年8月5日
    增加Config类
        目前仅支持读取 get 
        排除Yii的主要配置如 main test bootstrap codeception
        所有配置根据文件命名储存在数组中
            如 a.php
                [
                    'password'=>123456
                ]
            使用 Config::get('a.password');// 读取password，
            使用 Config::get('a'); //读取该配置文件的所有内容
        与common/config合并（如果存在）
    page 更名 Pagination 
    Request 更新支持获取 application/json 格式和application/xml格式
    
    2019年8月3日
    修复Request::param()获取所有请求参数时未返回内容问题
    优化Request 针对Boolean类型的识别判断
    新增 page 数据快速分页类
    
    2019年8月2日
    初始化项目
    增加Request
    增加模型的一对一、一对多、相对关联、远程一对一、远程一对多、多态关联、多态相对关联
    增加Cache
    增加Lang