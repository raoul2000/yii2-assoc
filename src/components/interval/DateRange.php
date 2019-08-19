<?php

namespace \app\components\interval;

class DateRange {
    /**
     * Start Range Value or NULL if the range is left opened
     */
    private $_start;
    /**
     * End Range Value or NULL if the range is right opened
     */
    private $_end;

    function __construct($start, $end = null)
    {
        if ($start !== null) {
            $this->_start = new DateTime($start . ' 00:00:00');
        }
        if ($end !== null) {
            $this->_end   = new DateTime($end)  . ' 00:00:00';
        }
        if ( $start !== null && $end !== null && $this->_end < $this->start) {
            throw new Exception('end date is before that start date');
        }
    }
    public function getStart() 
    {
        return $this->_start;
    }
    public function getEnd() 
    {
        return $this->_end;
    }
    public function isRightOpened()
    {
        return $this->end === null;
    }
    public function isLeftOpened()
    {
        return $this->start === null;
    }
    /**
     * Checks that a date in contained in a date range
     *
     * @param DateTime $date
     * @return boolean
     */
    public function containsDate($date)
    {
        // TODO: test date to be \DateTime
        if ($this->isLeftOpened()) {
            return $date <= $this->getEnd();
        } elseif ($this->isRightOpened()) {
            return $date >= $this->getStart();
        } else {
            return $date >= $this->getStart() && $date <= $this->getEnd();
        }
    }
    public function containsRange($range)
    {
        
    }

    public function overlaps($dateRange)
    {
        //TODO: check input arg

    }
}