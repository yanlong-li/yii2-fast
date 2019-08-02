<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2019/8/2
 *   IDE:    PhpStorm
 *   Desc:
 */


namespace yanlongli\yii2\fast;


class Request
{
    /**
     * @param null $name
     * @param null $default
     *
     * @return mixed
     * 修饰符    作用
     * s    强制转换为字符串类型
     * d    强制转换为整型类型
     * b    强制转换为布尔类型
     * a    强制转换为数组类型
     * f    强制转换为浮点类型
     */
    public static function param($name = null, $default = null)
    {
        if (is_string($name) && strlen(trim($name)) > 0) {

            $suffix = substr($name, -2);
            if (in_array($suffix, [
                '/s',//字符型
                '/d',//整型
                '/b',//布尔型
                '/a',//数组
                '/f',//浮点型
            ])) {
                $name = substr($name, 0, -2);
            } else {
                $suffix = null;
            }

            $params = array_merge(self::get(), self::post());
            if (isset($params[$name])) {
                return self::modification($params[$name], $suffix);
            }

            return $default;

        }

        return null;
    }

    public static function get($name = null, $default = null)
    {
        return \Yii::$app->request->get($name, $default);
    }

    public static function post($name = null, $default = null)
    {
        return \Yii::$app->request->post($name, $default);
    }

    public static function isGet()
    {
        return \Yii::$app->request->isGet;
    }

    public static function modification($value, $type)
    {
        $_value = null;
        switch ($type) {
            case '/s':
                $_value = (string)$value;
                break;
            case '/d':
                $_value = (integer)$value;
                break;
            case '/b':
                if ($value === 'true') {
                    $value = true;
                }
                if ($value === 'false') {
                    $value = false;
                }
                $_value = (boolean)$value;
                break;
            case '/a':
                $_value = (array)$value;
                break;
            case '/f':
                $_value = (float)$value;
                break;
            default:
                $_value = $value;
                break;
        }

        return $_value;
    }

    /**
     * 生成请求令牌
     * @access public
     *
     * @param $user
     *
     * @return string
     */
    public static function buildToken()
    {
        $str = microtime(true);
        $str .= uniqid($str);

        return strtoupper(md5($str));
    }

    public static function ip()
    {
        return \Yii::$app->request->getUserIP();
    }
}