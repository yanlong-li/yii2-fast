<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2019/8/3
 *   IDE:    PhpStorm
 *   Desc:
 */


namespace yanlongli\yii2\fast;


use yii\data\Pagination;
use yii\db\ActiveQuery;

trait Page
{

    protected $pageConfig = [];

    /**
     * @param        $model ActiveQuery
     *
     *
     * @param array  $param
     *
     * @return array
     */
    public function page($model, $param = []) {

        $pageIndex = Request::param('page/d');
        $pageSize  = Request::param('size/d');

        $pages      = new Pagination(array_merge([
            'page'       => $pageIndex - 1,
            'totalCount' => $model->count(),
            'pageSize'   => $pageSize,
        ], $this->pageConfig));
        $_queryData = $model->offset($pages->offset)->limit($pages->limit)->asArray()->all();

        if ( isset($param['modifyData']) ) {
            $_queryData = call_user_func($param['modifyData'], $_queryData);
        }

        $data = [
            'size'  => (int)$pages->pageSize,
            'total' => (int)$pages->totalCount,
            'page'  => (int)$pages->page + 1,
            'count' => (int)$pages->pageCount,
            'list'  => $_queryData,
        ];

        return $data;
    }
}