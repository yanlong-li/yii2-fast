<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2019/8/5
 *   IDE:    PhpStorm
 *   Desc: 这是Yii的标准化自动实例化类的启动类，非提供给开发者使用
 */


namespace yanlongli\yii2\fast;


use yii\base\Application;
use yii\base\BootstrapInterface;

class FastBootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->on(Application::EVENT_BEFORE_REQUEST, function () {
            // 初始化加载配置文件
            Config::init();
            // 初始化语言支持
//            Lang::init();
        });
    }
}
