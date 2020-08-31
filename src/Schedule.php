<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2020/7/27
 *   IDE:    PhpStorm
 *   Desc:  队列任务，最小频率为每分钟一次，最大频率推荐不超过1个月
 */

namespace yanlongli\yii2\fast;

use yanlongli\yii2\fast\lib\schedule\Closure;
use yanlongli\yii2\fast\lib\schedule\Command;
use yanlongli\yii2\fast\lib\schedule\Exec;
use yanlongli\yii2\fast\lib\schedule\Task;

/**
 * 队列任务
 * Class Schedule
 * @package yanlongli\yii2\fast
 */
abstract class Schedule extends \yii\base\Controller
{

    /**
     * @var Command[]
     */
    protected $commands = [];

    protected $startTime;
    protected $endTime;
    protected $degree = 0;

    /**
     *  项目入口
     */
    final public function actionIndex()
    {
        $nowTime = time();

        // 时间异常
        if (0 === $nowTime) {
            return;
        }

        $this->degree = (int)($nowTime  / 60);

        // 启动时间
        $this->startTime = microtime(true);
        // 清空任务
        $this->commands = [];
        /**
         * 收集计划任务
         */
        $this->schedule();
        $this->runTask();
        // 调度结束
        $this->endTime = microtime(true);
    }

    final protected function runTask()
    {
        foreach ($this->commands as $command) {
            try {
                //判断执行频率的处理
                if ($command->frequencyCheck($this->degree)) {
                    $command->handleRun();
                }
                // 执行任务
            } catch (\Exception $exception) {
                // 处理错误异常
                \Yii::warning("[code:{$exception->getCode()}][file:{$exception->getFile()}][line:{$exception->getLine()}] : " . $exception->getMessage());
            }
        }
    }

    /**
     * @return void
     */
    abstract public function schedule();


    public function command($command)
    {
        $command = new Command($command);
        $this->commands[] = &$command;
        return $command;
    }

    /**
     * @param string $command
     * @return Command
     */
    public function exec($command)
    {
        $command = is_array($command) ? implode(' ', $command) : $command;

        $command = new Exec($command);
        $this->commands[] = &$command;
        return $command;
    }

    /**
     * @param \Closure $command
     * @param mixed ...$parameter
     * @return Command
     */
    public function call(\Closure $command, ...$parameter)
    {
        $command = new Closure($command, $parameter);
        $this->commands[] = &$command;
        return $command;
    }

    /**
     * @param Task $task
     * @return Command
     */
    public function task($task)
    {
        if (!($task instanceof Task)) {
            new \Exception("Object Not instanceof Task");
        }
        $this->commands[] = &$task;
        return $task;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    public function actionWorker()
    {
        while (true) {
            $this->actionIndex();
            // 睡眠整分校准
            sleep(strtotime(date("Y-m-d H:i", strtotime("+1 minute"))) - time());
        }
    }


}
