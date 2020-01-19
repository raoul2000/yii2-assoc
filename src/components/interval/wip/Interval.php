<?php

namespace \app\components\interval\wip;

class Interval {

    private $start;
    private $end;

    function __construct($start, $end, IntervalValidator $validator) 
    {
        if ($this->validator->validateInterval($start, $end) !== true) {
            throw new \Exception('invalid interval');
        }
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }
}
