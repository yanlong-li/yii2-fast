<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2019/8/3
 *   IDE:    PhpStorm
 *   Desc:
 */


namespace yanlongli\yii2\fast;


class Controller extends \yii\web\Controller
{
    // 渲染html页面
    public function render($view = null, $params = [])
    {
        if ($view == null) {
            $view = $this->action->id;
        }

        return parent::render($view, array_merge($this->assigns, $params));
    }

    protected $assigns = [];

    public function assign($name, $value = null)
    {
        if (is_string($name)) {
            $this->assigns[$name] = $value;
        } else if (is_array($name)) {
            $this->assigns = array_merge($this->assigns, $name);
        }
    }
}