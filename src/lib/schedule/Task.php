<?php


namespace yanlongli\yii2\fast\lib\schedule;

use Exception;

/**
 * 基础任务抽象类
 * Class Task
 * @package yanlongli\yii2\fast\lib\schedule
 */
abstract class Task
{

    use Frequency;

    /**
     * 任务必须实现该方法
     * @return mixed
     */
    abstract protected function run();


    final public function handleRun()
    {
        try {
            $result = $this->run();
            $this->handleSuccess($result);
        } catch (Exception $exception) {
            $this->handleError($exception);
        }
    }

    /**
     * 错误时的处理
     * @param $exception \Exception
     */
    protected function handleError($exception)
    {

    }

    /**
     * 任务执行成功时的返回
     * 注意！！！ 这里不代表实际任务执行成功。
     * @param $response string
     */
    protected function handleSuccess($response)
    {

    }
}
