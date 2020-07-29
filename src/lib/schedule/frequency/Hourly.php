<?php


namespace yanlongli\yii2\fast\lib\schedule\frequency;


trait Hourly
{

    /**
     * @var int|int[]
     */
    protected $minuteOnTime = null;

    /**
     * @param int $interval 每隔几小时执行一次，默认每小时都执行一次
     * @return $this
     */
    public function hourly($interval = 0)
    {
        $this->frequency += $interval * strtotime('+1 hour', 0);
        return $this;
    }

    /**
     * 在某个分钟
     * @param int|int[] $interval
     * @return $this
     */
    public function hourlyOn($interval = 0)
    {
        $this->minuteOnTime = $interval;
        return $this;
    }

    protected function hourlyOnCheck()
    {
        $t = (int)date('i');
        return $this->minuteOnTime === null ?: (is_array($this->minuteOnTime) ? in_array($t, $this->minuteOnTime) : $t === $this->minuteOnTime);
    }

}
