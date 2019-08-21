Yii2 快速开发扩展插件
=

> 支持Config、Lang、Request、AR(db/model)、Cache、Pagination、Controller

## 前言

本扩展包是作者长期使用 `ThinkPHP` 和 `Laravel` 框架之下,养成的一些习惯。奈何Yii2框架不支持，于是工作过程中开发并一点点完善此扩展。

## 介绍


仿照 `ThinkPHP` 和 `Laravel` 框架编写此扩展，所以很多代码可能会直接由这两个框架中的代码直接复制过来，也有直接仿照源代码进行细微调整以适应Yii2框架。如果有问题欢迎来`Gitee`提交问题。
同时在项目内也会附带上两个框架的开源许可协议。

## 计划支持

    Request、Cookie、Session、Cache、Db
    
    以及支持表模型的多态关联、自动化的远程关联
## 使用方法
### Request
#### 变量获取
变量获取使用 Request 类的如下方法及参数：

变量类型方法('变量名/变量修饰符','默认值')
变量类型方法包括：

|方法|描述|备注|
|---|---|---|
|get|获取 $_GET 变量|
|post|获取 $_POST 变量|支持JSON、XML|
|param|参数集合|支持获取get和post的集合，以Post为主合并数组|
|except|参数过滤|该方法第一个参数可过滤指定的参数，兼容字符串（用英文逗号分隔）、数组|
|only|指定参数|该方法用于获取列表内的输入参数，与filter相反|
### Config
|方法|描述|备注|
|---|---|---|
|get|获取配置|支持递归获取`(site.name)`|
|set|设置配置|支持递归`('site.name','app')`|
### Controller
    assign 设置视图参数 支持数组批量设置
    render 和Yii作用一样，无需传递视图名称，默认为当前方法名称
### Cache
    get 获取
    set 设置
    delete 删除
    has 判断是否存在
    pull 获取并删除
    inc 自增 支持步长
    dec 自减 支持步长
    clear 清空所有缓存
### ActiveRecord
    morphOne 多态一对一关联
    morphMany 多态一对多关联
    morphTo 多态反向关联
    getMorphAliasName 多态类获取别名
    hasOneThrough 远程一对一关联
    hasManyThrough 远程一对多关联
    hasOne 一对一关联
    hasMany 一对多关联
    belongsTo 反向关联
### Pagination
    page($model,$param) 设定好条件的模型，参数 [pageConfig=>[page,totalCount...]] 设置分页类的参数
### Lang
    自动识别当前浏览器的语言 目前支持中文地区和英文地区
    t() 作用同 Yii::t()
    getLang() 获取当前语言
    setLang 设置语言
    langAliases 根据别名获取语言
    init 初始化语言分类 默认为 app
### Cookie
    set 设置
    get 获取
    deltete 删除
    clear 清空
    has 判断是否存在
### Validator
[验证器请参考ThikPHP](https://www.kancloud.cn/manual/thinkphp6_0/1037623)
部分功能未实现

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
    2019年8月21日
    修复Request在接收Json或XML格式数据时数据格式不正确导致报错问题，返回空数组或默认值
    
    2019年8月14日
    紧急修复，Lang 初始化 调用 \Yii::$app->request 在命令行模式 yii\base\Request 类非 yii\web\Request 问题导致报错问题
    Config 新增支持递归参数合并
    Request 新增Method判断，参考 ThinkPHP
    新增 Cookie操作，源码来自 ThinkPHP
    Config 新增支持子目录配置文件，合并到同名主目录配置文件（如果存在）
    调整 Request 参数修饰符，支持多字符类型 来源 ThinkPHP
    新增 Validator 验证器 来源 ThinkPHP
    
    2019年8月13日
    修复 Config 连贯模式不存在的主参数导致报错问题

    2019年8月10日
    紧急修复语言包别名加载错误问题
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