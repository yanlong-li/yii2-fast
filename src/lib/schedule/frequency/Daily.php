<?php


namespace yanlongli\yii2\fast\lib\schedule\frequency;


trait Daily
{
    /**
     * 内小时
     * @var int|int[]
     */
    protected $hourOnTime = null;

    /**
     * 间隔时间
     * @param int $interval
     * @return $this
     */
    public function daily($interval = 0)
    {
        $this->frequency += $interval * strtotime('+1 day', 0);
        return $this;
    }

    /**
     * 在某个小时
     * @param int|int[] $interval
     * @return $this
     */
    public function dailyOn($interval = 0)
    {
        $this->hourOnTime = $interval;
        return $this;
    }

    protected function dailyOnCheck()
    {
        $h = (int)date('H');
        return $this->hourOnTime === null ?: (is_array($this->hourOnTime) ? in_array($h, $this->hourOnTime) : $h === $this->hourOnTime);
    }


}
