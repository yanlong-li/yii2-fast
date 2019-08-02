<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2019/8/2
 *   IDE:    PhpStorm
 *   Desc:    Yii2 缓存快速操作
 */


namespace yanlongli\yii2\fast;


class Cache
{
    /**
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        return \Yii::$app->cache->get($key);
    }

    /**
     * @param string $key
     * @param string $value
     * @param null $duration
     * @param null $dependency
     * @return bool
     */
    public static function set($key, $value, $duration = null, $dependency = null)
    {
        return \Yii::$app->cache->set($key, $value, $duration, $dependency);
    }

    public static function has($key)
    {
        return \Yii::$app->cache->exists($key);
    }

    public static function delete($key)
    {
        return \Yii::$app->cache->delete($key);
    }

    public static function pull($key)
    {
        $data = self::get($key);
        self::delete($key);

        return $data;

    }

    /**
     * @param string $key 键
     * @param int $step 步进
     * @param int $s 缓存时间
     * @return int|mixed 返回结果
     */
    public static function inc($key, $step = 1, $s = 0)
    {
        $number = self::get($key);
        $number += $step;
        self::set($key, $number, $s);

        return $number;
    }

    /**
     * @param string $key 键
     * @param int $step 步进
     * @param int $s 缓存时间
     * @return int|mixed 返回结果
     */
    public static function dec($key, $step = 1, $s = 0)
    {
        return self::inc($key, -$step, $s);
    }

    // 清空缓存
    public static function clear()
    {
        \Yii::$app->cache->flush();
    }

}