<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2019/8/3
 *   IDE:    PhpStorm
 *   Desc:
 */


namespace yanlongli\yii2\fast;

use yii\db\ActiveQuery;

trait Pagination
{

    /**
     * @param ActiveQuery $model
     * @param array $param
     *
     * @return array
     */
    public function page($model, $param = [])
    {

        $pageIndex = Request::param('page/d');
        $pageSize = Request::param('size/d');

        $pages = new \yii\data\Pagination(array_merge([
            'page' => $pageIndex - 1,
            'totalCount' => $model->count(),
            'pageSize' => $pageSize,
        ], isset($param['pageConfig']) ? $param['pageConfig'] : []));
        $_queryData = $model->offset($pages->offset)->limit($pages->limit)->asArray()->all();

        // 这里其实不用传递，返回的数组 data.list 可以自行处理
        // 原来这里是直接返回json数据，所以用匿名函数提前处理
        if (isset($param['modifyData'])) {
            $_queryData = call_user_func($param['modifyData'], $_queryData);
        }

        return [
            'size' => (int)$pages->pageSize,
            'total' => (int)$pages->totalCount,
            'page' => (int)$pages->page + 1,
            'count' => (int)$pages->pageCount,
            'list' => $_queryData,
        ];
    }
}
