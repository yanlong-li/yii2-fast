<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2019/8/2
 *   IDE:    PhpStorm
 *   Desc:    表模型快速操作类
 */


namespace yanlongli\yii2\fast;


use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @var string|array $primaryKey 模型主键
     */
    public $primaryKey = 'id';

    //region 多态关联
    // 参考 ThinkPHP

    // 定义别名
    public static $morphAlias = [];
    // 关联字段前缀
    public static $morph = 'able';

    //region 多态一对一

    /**
     * 多态关联 一对一 关联定义
     * @access public
     *
     * @param string|static $model 模型名
     * @param string|array $morph 多态字段信息
     *
     * @return ActiveQuery
     */
    public function morphOne($model, $morph = null)
    {
        if (is_array($morph)) {
            list($morphType, $foreignKey) = $morph;
        } else {
            $morphType = static::getMorphType($morph);
            $foreignKey = static::getMorphKey($morph);
        }

        return $this->hasOne($model, [$foreignKey => 'id'])->where([$morphType => $model::getMorphAliasName(static::class)]);
    }
    //endregion

    //region 多态一对多
    /**
     * 多态关联 一对多 关联定义
     * @access public
     *
     * @param string|static $model 模型名
     * @param string|array $morph 多态字段信息
     *
     * @return ActiveQuery
     */
    public function morphMany($model, $morph = null)
    {
        if (is_array($morph)) {
            list($morphType, $foreignKey) = $morph;
        } else {
            $morphType = static::getMorphType($morph);
            $foreignKey = static::getMorphKey($morph);
        }

        return $this->hasMany($model, [$foreignKey => 'id'])->where([$morphType => $model::getMorphAliasName(static::class)]);
    }
    //endregion

    //region 多态反向关联
    /**
     * MORPH TO 多态关联 关联定义
     * @access public
     *
     * @param string|array $morph 多态字段信息
     * @param array $alias 多态别名定义
     *
     * @return mixed
     */
    public function morphTo($morph = null, array $alias = [])
    {
        // 记录当前关联信息
        if (is_array($morph)) {
            list($morphType, $foreignKey) = $morph;
        } else {
            $morphType = static::getMorphType($morph);
            $foreignKey = static::getMorphKey($morph);
        }

        $morphModelName = $this->$morphType;
        $primaryKey = null;
        $alias = Config::arrayMerge(static::$morphAlias, $alias);
        if (is_array($alias) && !empty($alias)) {
            if (isset($alias[$this->$morphType])) {
                $morphModelName = $alias[$this->$morphType];
                if (is_array($morphModelName) && !empty($morphModelName)) {
                    if (isset($morphModelName[1])) {
                        $primaryKey = $morphModelName[1];
                    }
                    $morphModelName = $morphModelName[0];
                }
            }
        }

        if (!$primaryKey) {
            $morphModel = new $morphModelName();
            $primaryKey = $morphModel->primaryKey ?: 'id';
        }

        return $this->hasOne($morphModelName, [$primaryKey => $foreignKey]);
    }
    //endregion

    //region 多态关联 获取别名
    /**
     * 多态关联 获取别名
     *
     * @param $class
     *
     * @return string
     */
    public static function getMorphAliasName($class)
    {
        if (!is_string($class)) {
            if (is_object($class)) {
                $class = get_class($class);
            }
        }
        //获取反转后的数组
        $morphAlias = array_flip(static::$morphAlias);
        if (isset($morphAlias[$class])) {
            $class = $morphAlias[$class];
        }

        return $class;

    }
    //endregion

    //region 获取多态关联类型字段
    /**
     * 获取多态关联类型字段
     *
     * @param $morph
     *
     * @return string
     */
    public static function getMorphType($morph = null)
    {
        $morph = $morph ?: static::$morph;

        return $morph . '_type';
    }
    //endregion

    //region 获取多态关联类型主键
    /**
     * 获取多态关联类型主键
     *
     * @param $morph
     *
     * @return string
     */
    public static function getMorphKey($morph = null)
    {
        $morph = $morph ?: static::$morph;

        return $morph . '_id';
    }
    //endregion

    //endregion 多态关联结束

    //region 远程关联
    // 参考Laravel ThinkPHP
    //region 远程一对多
    /**
     * 远程一对多关联
     * @param string $related 关联表模型名
     * @param string $through 中间表模型名
     * @param string $firstKey 中间表外键名
     * @param string $relatedKey 关联表键名
     * @param string $localKey 本地表外键名
     * @param string $secondLocalKey 本地表外键名
     * @return ActiveQuery
     * @throws InvalidConfigException
     * Tp和Laravel 是一致的，但是我绕了半天越来余额糊涂，所以第四个参数改变了 按照原文档 “第四个参数表示最终模型的外键名” ，文档中的最终模型是post模型，post模型的外键没有在其余两张表中出现，所以不清楚到底是什么情况
     */
    public function hasManyThrough($related, $through, $firstKey = null, $relatedKey = null, $localKey = null, $secondLocalKey = null)
    {
        return $this->hasThrough($related, $through, $firstKey, $relatedKey, $localKey, $secondLocalKey, false);
    }
    //endregion

    //region 远程一对一
    /**
     * 远程一对一关联
     * @param string $related 关联表模型名
     * @param string $through 中间表模型名
     * @param string $firstKey 中间表外键名
     * @param string $relatedKey 关联表键名
     * @param string $localKey 本地表外键名
     * @param string $secondLocalKey 本地表外键名
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function hasOneThrough($related, $through, $firstKey = null, $relatedKey = null, $localKey = null, $secondLocalKey = null)
    {


        return $this->hasThrough($related, $through, $firstKey, $relatedKey, $localKey, $secondLocalKey);
    }
    //endregion

    //region 远程关联基础
    /**
     * @param string $related 关联表模型名
     * @param string $through 中间表模型名
     * @param string $firstKey 中间表外键名
     * @param string $relatedKey 关联表键名
     * @param string $localKey 本地表外键名
     * @param string $secondLocalKey 本地表外键名
     * @param bool $one 一对一 还是一对多
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    protected function hasThrough($related, $through, $firstKey = null, $relatedKey = null, $localKey = null, $secondLocalKey = null, $one = true)
    {
        /**
         * @var $throughModel static
         */
        $throughModel = new $through();
        $throughTableName = $throughModel::tableName();

        if (is_null($firstKey)) {
            $firstKey = static::getForeignKey($through);
        }
        if (is_null($secondLocalKey)) {
            $secondLocalKey = static::getForeignKey(static::class);
        }

        if (is_null($localKey)) {
            $localKey = 'id';
        }
        if (is_null($relatedKey)) {
            $relatedKey = 'id';
        }
        if ($one) {
            return $this->hasOne($related, [$firstKey => $relatedKey])->viaTable($throughTableName, [$secondLocalKey => $localKey]);
        } else {
            return $this->hasMany($related, [$firstKey => $relatedKey])->viaTable($throughTableName, [$secondLocalKey => $localKey]);
        }

    }
    //endregion

    //endregion 结束远程关联

    //region 基本关联
    //region 一对一关联
    /**
     * 一对一关联 拆分关联 兼容旧关联
     * @param $class
     * @param string|null $foreignKey 关联模型主键
     * @param string|null $localKey 本地外键
     * @return ActiveQuery
     */
    public function hasOne($class, $foreignKey = null, $localKey = null)
    {
        return $this->hasRelation($class, $foreignKey, $localKey, true);
    }
    //endregion

    //region 一对多关联
    /**
     * @param $class
     * @param string|null $foreignKey 关联模型主键
     * @param string|null $localKey 本地外键
     * @return ActiveQuery
     */
    public function hasMany($class, $foreignKey = null, $localKey = null)
    {
        return $this->hasRelation($class, $foreignKey, $localKey, false);
    }
    //endregion

    //region 反向关联
    /**
     * @param $class
     * @param null $foreignKey
     * @param null $localKey
     * @return ActiveQuery
     */
    public function belongsTo($class, $foreignKey = null, $localKey = null)
    {
        if (is_null($foreignKey)) {
            /**
             * @var $classObject static
             */
            $classObject = new $class();
            $foreignKey = $classObject->primaryKey;
        }
        if (is_null($localKey)) {
            $localKey = static::getForeignKey($class);
        }
        return $this->hasRelation($class, $foreignKey, $localKey, true);
    }
    //endregion

    //region 基础关联
    /**
     * @param $class
     * @param string|null $foreignKey 关联模型主键
     * @param string|null $localKey 本地外键
     * @param bool $one
     * @return ActiveQuery
     */
    protected function hasRelation($class, $foreignKey = null, $localKey = null, $one = true)
    {
        $link = [];
        if (!is_array($foreignKey)) {
            if (is_null($foreignKey)) {
                $foreignKey = static::getForeignKey(static::class);
            }
            if (is_null($localKey)) {
                $localKey = $this->primaryKey;
            }
            $link[$foreignKey] = $localKey;
        } else {
            $link = $foreignKey;
        }

        if ($one) {
            return parent::hasOne($class, $link);
        } else {
            return parent::hasMany($class, $link);
        }
    }
    //endregion

    //endregion 结束基本关联

    /**
     * 获取模型的默认外键名
     * @access protected
     * @param string $name 模型名
     * @return string
     */
    protected function getForeignKey($name)
    {
        if (strpos($name, '\\')) {
            $name = static::classBaseName($name);
        }

        return static::parseName($name) . '_id';
    }

    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @access public
     * @param string $name 字符串
     * @param integer $type 转换类型
     * @param bool $ucFirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name = null, $type = 0, $ucFirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucFirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }

    /**
     * 获取类名(不包含命名空间)
     * @access public
     * @param string|object $class
     * @return string
     */
    public static function classBaseName($class)
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }

    /**
     * 获取模型对象的主键
     * @access public
     * @return string|array
     */
    public function getPk()
    {
        return $this->primaryKey;
    }

    /**
     * 判断一个字段名是否为主键字段
     * @access public
     * @param string $key 名称
     * @return bool
     */
    protected function isPk($key)
    {
        $pk = $this->getPk();

        if (is_string($pk) && $pk == $key) {
            return true;
        } elseif (is_array($pk) && in_array($key, $pk)) {
            return true;
        }

        return false;
    }

    /**
     * 获取模型对象的主键值
     * @access public
     * @return mixed
     */
    public function getKey()
    {
        return $this->getPrimaryKey();
    }

    /**
     * 模型转数组，并同时返回关联数据
     * @param array $fields
     * @param array $expand
     * @param bool  $recursive
     * @return mixed
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = parent::toArray($fields, $expand, $recursive);
        $related = $this->getRelatedRecords();
        foreach ($related as $key => $item) {
            $data[$key] = $item->toArray();
        }
        return $data;
    }
}
