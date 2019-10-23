<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2019/8/3
 *   IDE:    PhpStorm
 *   Desc:
 */


namespace yanlongli\yii2\fast;


class Config
{
    protected static $config = null;
    protected static $init = false;

    public static function init()
    {
        //加载公共配置
        $commonConfigPath = \Yii::$app->basePath . '/../common/config';
        if (is_dir($commonConfigPath)) {
            static::LoadConfigFile($commonConfigPath);
        }
        //加载模块配置
        $moduleConfigPath = \Yii::$app->basePath . '/config';
        static::LoadConfigFile($moduleConfigPath);
    }

    /**
     * 获取配置参数
     * 兼容 key.key
     * key. 获取key下的所有数据 value
     *
     * @param string $name
     * @param mixed $default
     * @param mixed $config
     *
     * @return array|mixed|null
     */
    public static function get($name = '', $default = null, $config = null)
    {
        if ($config == null) {
            if (is_null(static::$config)) {
                static::$config['params'] = \Yii::$app->params;
            }
            $config = static::$config;
        }

        if(isset($config[$name])){
            return $config[$name];
        }

        $name = explode('.', $name);
        if (count($name) == 1) {
            if (trim($name[0]) == '') return $config;

            return isset($config[$name[0]]) ? $config[$name[0]] : $default;
        } else {
            if (isset($config[$name[0]])) {
                $newName = $name[0];
                unset($name[0]);
                $name = implode('.', $name);
            } else {
                return $default;
            }

            return static::get($name, $default, $config[$newName]);
        }
    }

    /**
     * 加载配置文件 已支持递归合并数组
     * @param $path
     * @param null $config
     * @return null
     */
    public static function LoadConfigFile($path, &$config = null)
    {
        if (is_null($config)) {
            $config = &static::$config;
        }
        if (is_file($path)) {
            //文件伪装成文件夹文件夹
            $basename = basename($path);
            $temp[] = $basename;
            $path = substr($path, 0, -strlen($basename));
        } else {
            //读取文件夹
            $temp = scandir($path);
        }
        // 反向排序文件顺序 因为 -local.php 的 - 比. 的优先级高
        rsort($temp);
        //遍历文件夹
        foreach ($temp as $v) {
            $a = $path . '/' . $v;
            if (is_dir($a)) {
                //忽略子目录
                if ($v == '.' || $v == '..') {//判断是否为系统隐藏的文件.和..  如果是则跳过否则就继续往下走，防止无限循环再这里。
                    continue;
                }
                if (!isset($config[$v]))
                    $config[$v] = [];
                // 深度加载文件
                static::LoadConfigFile($a, $config[$v]);
            } else {
                if (substr($v, 0, 1) == '.') {
                    continue;
                } else {
                    if (substr($v, -10) === '-local.php') {
                        $k = substr($v, 0, -10);
                    } else {
                        $k = substr($v, 0, -4);
                    }
                    // 过滤 Yii 的主要配置文件
                    if (in_array($k, ['main', 'codeception', 'bootstrap', 'test']) || substr($v, -4) !== '.php') {
                        continue;
                    }
                    if (file_exists($a)) {
                        $_config = require $a;
                        if (isset($config[$k])) {
                            $config[$k] = static::arrayMerge($config[$k], $_config);
                        } else {
                            $config[$k] = $_config;
                        }
                    }
                }
            }

        }
        return $config;
    }

    /**
     * 设置配置参数 name为数组则为批量设置
     * @access public
     * @param string|array $name 配置参数名（支持无限层级 .号分割）
     * @param mixed $value 配置值
     * @param array $config 配置，默认为Config
     * @return mixed
     */
    public static function set($name, $value = null, &$config = null)
    {
        if (is_null($config)) {
            $config = &static::$config;
        }
        if (is_string($name)) {

            $name = explode('.', $name);

            // 一级配置
            if (count($name) === 1) {
                $config[$name[0]] = $value;
                return true;
            }
            if (!isset($config[$name[0]])) {
                $config[$name[0]] = [];
            }
            $newName = $name;
            unset($newName[0]);
            $newName = implode('.', $newName);
            return static::set($newName, $value, $config[$name[0]]);
        } elseif (is_array($name)) {
            if (is_null($value)) {

                foreach ($name as $key => $value) {
                    static::$config[$key] = static::arrayMerge(static::$config[$key], $value);
                }
                return true;

            }
        }

        return false;
    }

    /**
     * 获取一级配置
     * @param $name
     * @return array
     */
    public static function pull($name)
    {
        if ($name && isset(static::$config[$name])) {
            return static::$config[$name];
        }
        return [];
    }

    /**
     * 递归合并两个数组
     * 如果要合并的两个数组的对应键并非都是数组（无法合并），则以第二个数组的键值覆盖第一个数组对应的键值
     * @param array $array1 主数组
     * @param array $array2 附加数组
     * @return array 返回主数组
     */
    public static function arrayMerge($array1, $array2)
    {
        if (!is_array($array1))
            $array1 = [];
        if (!is_array($array2)) {
            $array2 = [];
        }
        /**
         * 遍历数组2
         */
        foreach ($array2 as $key2 => $item2) {
            // 如果附加数组的某个键的值是数组 ，并且 主数组的对应键的值也是数组 那么合并这两个数组
            if (is_array($item2) && isset($array1[$key2]) && is_array($array1[$key2])) {
                $array1[$key2] = static::arrayMerge($array1[$key2], $item2);
            } else {
                $array1[$key2] = $array2[$key2];
            }
        }
        return $array1;
    }
}