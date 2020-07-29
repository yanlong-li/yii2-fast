<?php


namespace yanlongli\yii2\fast\lib\schedule\frequency;


trait Monthly
{
    /**
     * @var int|int[]
     */
    protected $dailyOnTime = null;

    /**
     * @param $interval
     * @return $this
     */
    public function monthly($interval)
    {
        $this->frequency += $interval * strtotime('+1 month', 0);
        return $this;
    }

    /** 1 - [29-31],如果为31号则无31号的月份不会执行任务
     * 在某天
     * @param int|int[] $interval
     * @return $this
     */
    public function monthlyOn($interval = 0)
    {
        $this->dailyOnTime = $interval;
        return $this;
    }

    protected function monthlyOnCheck()
    {
        $t = (int)date('d');
        return $this->dailyOnTime === null ?: (is_array($this->dailyOnTime) ? in_array($t, $this->dailyOnTime) : $t === $this->dailyOnTime);
    }

}
