<?php


namespace yanlongli\yii2\fast\lib\schedule\frequency;

trait Minute
{
    /**
     * @param int $interval 0-59 每隔几分钟执行一次，默认每分钟都执行一次
     * @return $this
     */
    public function everyMinute($interval = 0)
    {
        $this->frequency += $interval * 60;
        return $this;
    }
}
