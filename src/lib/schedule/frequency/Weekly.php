<?php


namespace yanlongli\yii2\fast\lib\schedule\frequency;


trait Weekly
{

    /**
     * @var int|int[]
     */
    protected $weeklyOnTime = null;

    /**
     * @param int $interval 0-6
     * @return Weekly
     */
    public function weekly($interval = 0)
    {
        $this->frequency += $interval * strtotime('+1 week', 0);
        return $this;
    }

    /**
     * 在某天 0-6 ，周日~六
     * @param int|int[] $interval
     * @return $this
     */
    public function weeklyOn($interval = 0)
    {
        $this->weeklyOnTime = $interval;
        return $this;
    }

    protected function weeklyOnCheck()
    {
        $t = (int)date('w');
        return $this->weeklyOnTime === null ?: (is_array($this->weeklyOnTime) ? in_array($t, $this->weeklyOnTime) : $t === $this->weeklyOnTime);
    }
}
