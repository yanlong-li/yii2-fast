<?php


namespace yanlongli\yii2\fast\lib\schedule;


class Closure extends Task
{

    public function __construct($command, $parameter)
    {
        $this->command = $command;
        $this->parameter = $parameter;
    }

    /**
     * @var \Closure
     */
    protected $command;
    protected $parameter;

    public function run()
    {
        return call_user_func($this->command, ...$this->parameter);
    }
}
