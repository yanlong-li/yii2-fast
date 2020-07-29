<?php


namespace yanlongli\yii2\fast\lib\schedule;

use yanlongli\yii2\fast\lib\schedule\frequency\Daily;
use yanlongli\yii2\fast\lib\schedule\frequency\Hourly;
use yanlongli\yii2\fast\lib\schedule\frequency\Minute;
use yanlongli\yii2\fast\lib\schedule\frequency\Monthly;
use yanlongli\yii2\fast\lib\schedule\frequency\Quarterly;
use yanlongli\yii2\fast\lib\schedule\frequency\Weekly;
use yanlongli\yii2\fast\lib\schedule\frequency\Yearly;

/**
 * Trait Frequency
 * 频率控制
 * 参考Laravel7.x框架
 * @package yanlongli\yii2\fast\lib\schedule
 */
trait Frequency
{

    use Yearly;
    use Quarterly;
    use Monthly;
    use Weekly;
    use Daily;
    use Hourly;
    use Minute;


    /**
     * @var int 秒
     */
    protected $frequency = 0;

    /**
     * @var int 开始时间
     */
    protected $startTime = 0;
    /**
     * @var int 结束时间
     */
    protected $endTime = 0;

    /**
     * 开始执行时间 Y-m-d H:i
     * @param $time
     * @return $this
     */
    public function startTime($time)
    {
        $this->startTime = strtotime($time);
        return $this;
    }

    /**
     * 结束执行时间 Y-m-d H:i
     * @param $time
     * @return $this
     */
    public function endTime($time)
    {
        $this->endTime = strtotime($time);
        return $this;
    }

    /**
     * @param int $degree 上次执行时间或任务创建时间
     * @return bool
     */
    public function frequencyCheck($degree = 0)
    {
        $result = true;
        $result = $this->hourlyOnCheck() ? $result : false;
        $result = $this->dailyOnCheck() ? $result : false;
        $result = $this->weeklyOnCheck() ? $result : false;
        $result = $this->monthlyOnCheck() ? $result : false;
        $result = $this->yearlyOnCheck() ? $result : false;

        $result = $this->frequency === 0 || $degree % ($this->frequency / 60) === 0 ? $result : false;
        $result = (!$this->startTime || $this->startTime <= time()) ? $result : false;
        $result = (!$this->endTime || $this->endTime >= time()) ? $result : false;

        return $result;
    }
}
