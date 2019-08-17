<?php

namespace \app\components\interval;

class Interval {
    /**
     * Start Range Value or NULL if the range is left opened
     */
    private $_start;
    /**
     * End Range Value or NULL if the range is right opened
     */
    private $_end;

    function __construct($start, $end)
    {
        $this->_start = $start;
        $this->_end   = $end;
    }
    public function getStart() 
    {
        return $this->_start;
    }
    public function getEnd() 
    {
        return $this->_end;
    }
}