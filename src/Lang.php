<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2019/8/2
 *   IDE:    PhpStorm
 *   Desc:    国际化语言支持扩展
 */

namespace yanlongli\yii2\fast;


class Lang
{

    public static function t($message, $params = [], $category = null, $language = null)
    {
        $category = $category === null ? self::$category : $category;

        return \Yii::t($category, $message, $params, $language);
    }

    /**
     * 初始化语言包
     * @param string $category
     */
    public static function init($category = 'app')
    {
        static::$category = $category;
        \Yii::$app->language = static::getLang();
    }

    public static $category = 'app';

    public static function getLang()
    {
        // 先檢測用戶是否設定顯示語言
        if (Request::param('language/s')) {
            return static::langAliases(Request::param('language/s'));
        }
        // 先檢測用戶是否設定顯示語言
        if (isset($_COOKIE['language'])) {
            return $_COOKIE['language'];
        }
        // 處理瀏覽器首選語言
        if (self::getBrowserLanguage()) {
            return self::getBrowserLanguage();
        } else {
            return \Yii::$app->language;
        }
    }

    protected static function getBrowserLanguage()
    {
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // 首选语言集合
            $langString = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            // 分解组
            //array(7) { [0]=> string(8) "en,zh-TW" [1]=> string(8) "q=0.9,zh" [2]=> string(8) "q=0.8,ko" [3]=> string(11) "q=0.7,zh-CN" [4]=> string(9) "q=0.6,und" [5]=> string(11) "q=0.5,zh-HK" [6]=> string(5) "q=0.4" }
            $langGroups = explode(";", $langString);
            // 从分解的组中获取第一个支持的语言
            foreach ($langGroups as $langGroupString) {
                // en,zh-TW
                // 继续分解
                $langGroup = explode(",", $langGroupString);
                foreach ($langGroup as $lang) {
                    $_lang = self::langAliases($lang);
                    if ($_lang) {
                        return $_lang;
                    }
                }
            }
        }

        return null;
    }

    protected static function langAliases($lang)
    {

        if (is_null(self::$langAliases)) {
            self::$langAliases = require __DIR__ . './lib/lang.aliases.php';
        }
        $langKey = null;
        foreach (self::$langAliases as $key => $alias) {
            if (in_array(strtolower($lang), $alias)) {
                $langKey = $key;
                break;
            }
        }

        return $langKey;
    }

    public static $langAliases;

    /**
     * 设定语言，可根据Yii2官方国际化文档，或者参考 lang.aliases.php 进行设定
     * @param string $lang 语言
     */
    public function setLang($lang)
    {
        \Yii::$app->language = self::langAliases($lang);
    }
}