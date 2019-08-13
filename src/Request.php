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
        // 合并Get和Post数据，以Post为主覆盖Get数据
        $params = array_merge(static::get(), static::post());

        return static::input($params, $name, $default);
    }

    /**
     * 获取指定的参数
     * @access public
     * @param string|array $name 变量名
     * @param string $type 变量类型
     * @return mixed
     */
    public static function only($name, $type = 'param')
    {

        $param = static::$type();

        if (is_string($name)) {
            $name = explode(',', $name);
        }

        $item = [];
        foreach ($name as $key => $val) {

            if (is_int($key)) {
                $default = null;
                $key = $val;
            } else {
                $default = $val;
            }

            if (isset($param[$key])) {
                $item[$key] = $param[$key];
            } elseif (isset($default)) {
                $item[$key] = $default;
            }
        }

        return $item;
    }

    /**
     * 排除指定参数获取
     * @access public
     * @param string|array $name 变量名
     * @param string $type 变量类型
     * @return mixed
     */
    public static function except($name, $type = 'param')
    {
        $param = static::$type();
        if (is_string($name)) {
            $name = explode(',', $name);
        }

        foreach ($name as $key) {
            if (isset($param[$key])) {
                unset($param[$key]);
            }
        }

        return $param;
    }


    public static function get($name = null, $default = null)
    {
        $data = \Yii::$app->request->get($name, $default);
        return static::input($data, $name, $default);
    }

    /**
     * @param null $name
     * @param null $default
     * @return mixed
     */
    public static function post($name = null, $default = null)
    {
        $rawContentType = \Yii::$app->request->getContentType();
        if (($pos = strpos($rawContentType, ';')) !== false) {
            $contentType = substr($rawContentType, 0, $pos);
        } else {
            $contentType = $rawContentType;
        }
        $data = $default;
        switch ($contentType) {
            case 'application/json':
                $data = json_decode(file_get_contents('php://input'), true);
                break;
            case 'application/xml':
            case 'text/xml':
                $xml = simplexml_load_string(file_get_contents('php://input'), 'SimpleXMLElement', LIBXML_NOCDATA);
                $data = json_decode(json_encode($xml), true);
                break;
            case 'text/plain':
            case 'application/javascript':
            case 'text/html':
                break;
            case 'multipart/form-data':
            default:
                $data = \Yii::$app->request->post($name, $default);
                break;

        }

        return static::input($data, $name, $default);
    }

    /**
     * 是否为GET请求
     * @access public
     * @return bool
     */
    public static function isGet()
    {
        return \Yii::$app->request->isGet;
    }

    /**
     * 是否为POST请求
     * @access public
     * @return bool
     */
    public static function isPost()
    {
        return \Yii::$app->request->isPost;
    }

    /**
     * 是否为PUT请求
     * @access public
     * @return bool
     */
    public static function isPut()
    {
        return \Yii::$app->request->isPut;
    }

    /**
     * 是否为DELTE请求
     * @access public
     * @return bool
     */
    public static function isDelete()
    {
        return \Yii::$app->request->isDelete;
    }

    /**
     * 是否为HEAD请求
     * @access public
     * @return bool
     */
    public static function isHead()
    {
        return \Yii::$app->request->isHead;
    }

    /**
     * 是否为PATCH请求
     * @access public
     * @return bool
     */
    public static function isPatch()
    {
        return \Yii::$app->request->isPatch;
    }

    /**
     * 是否为OPTIONS请求
     * @access public
     * @return bool
     */
    public static function isOptions()
    {
        return \Yii::$app->request->isOptions;
    }

    /**
     * 是否为cli
     * @access public
     * @return bool
     */
    public static function isCli()
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * 当前是否ssl
     * @access public
     * @return bool
     */
    public static function isSsl()
    {
        return \Yii::$app->request->isSecureConnection;
    }

    /**
     * 当前是否Ajax请求
     * @access public
     * @param bool $ajax true 获取原始ajax请求
     * @return bool
     */
    public static function isAjax($ajax = false)
    {
        return \Yii::$app->request->isAjax;
    }

    /**
     * 当前是否Pjax请求
     * @access public
     * @return bool
     */
    public static function isPjax()
    {
        return \Yii::$app->request->isPjax;
    }

    public static function method()
    {
        return \Yii::$app->request->method;
    }

    /**
     * @param array $data
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected static function input($data, $name, $default)
    {
        $name = (string)$name;
        if ('' != $name) {
            // 解析name
            if (strpos($name, '/')) {
                list($name, $suffix) = explode('/', $name);
            }

            $data = static::getData($data, $name);

            if (is_null($data)) {
                return $default;
            }

            if (is_object($data)) {
                return $data;
            }
        }
        // 强制类型转换
        if (isset($suffix)) {
            return static::typeCast($data, $suffix);
        }
        return $data;
    }

    /**
     * 获取数据
     * @access public
     * @param array $data 数据源
     * @param string|false $name 字段名
     * @return mixed
     */
    protected static function getData(array $data, $name)
    {
        foreach (explode('.', $name) as $val) {
            if (isset($data[$val])) {
                $data = $data[$val];
            } else {
                return;
            }
        }

        return $data;
    }

    /**
     * 强制类型转换
     * @access public
     * @param string $value
     * @param string $type
     * @return mixed
     */
    public static function typeCast($value, $type)
    {
        $_value = null;
        switch ($type) {
            case 's':
                if (is_scalar($value)) {
                    $_value = (string)$value;
                } else {
                    throw new \InvalidArgumentException('variable type error：' . gettype($value));
                }
                break;
            case 'd':
                $_value = (integer)$value;
                break;
            case 'b':
                if (in_array(strtolower($value), ['true', 1, 't'])) {
                    $value = true;
                }
                if (in_array(strtolower($value), ['false', 0, 'f'])) {
                    $value = false;
                }
                $_value = (boolean)$value;
                break;
            case 'a':
                $_value = (array)$value;
                break;
            case 'f':
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