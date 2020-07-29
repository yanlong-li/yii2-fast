<?php


namespace yanlongli\yii2\fast\lib\schedule\frequency;


trait Quarterly
{

    /**
     * @param $at
     * @return $this
     */
    public function quarterly($at)
    {
        $this->frequency += $at * strtotime('+3 month', 0);
        return $this;
    }
}
