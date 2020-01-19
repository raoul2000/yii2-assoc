<?php

namespace \app\components\interval\wip;

class IntervalFactory {

    private $validator;

    function __construct($validator) 
    {
        $this->validator = $validator;
    }

    public function create($start, $end)
    {
        return new Interval($start, $end, $this->validator);
    }    
}
