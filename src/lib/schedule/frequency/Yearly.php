<?php


namespace yanlongli\yii2\fast\lib\schedule\frequency;


trait Yearly
{

    /**
     * @var int|int[] 0-11 1~12月
     */
    protected $monthlyOnTime = null;

    public function yearly($interval)
    {
        $this->frequency += $interval * strtotime('+1 year', 0);
        return $this;
    }

    /**
     * 某月 1 - 12
     * @param int|int[] $interval
     * @return $this
     */
    public function yearlyOn($interval = 0)
    {
        $this->monthlyOnTime = $interval;
        return $this;
    }

    protected function yearlyOnCheck()
    {
        $t = (int)date('m');
        return $this->monthlyOnTime === null ?: (is_array($this->monthlyOnTime) ? in_array($t, $this->monthlyOnTime) : $t === $this->monthlyOnTime);
    }
}
