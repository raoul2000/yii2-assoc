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
        if ($start === null && $end === null) {
            throw new Exception('one of start or end must not be null');
        }
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
    /**
     * Returns TRUE if this range has no end value and FALSE otherwise
     * A Right opened range is a range that has a start date and no end date.
     * Example : From 2019-08-17 ... and for ever in the future
     * 
     * @return boolean
     */
    public function isRightOpened()
    {
        return $this->end === null;
    }

    /**
     * Returns TRUE if this range has no start date value and FALSE otherwise
     * Example : since the begining of time ...and until 2019-08-17 
     *
     * @return boolean
     */
    public function isLeftOpened()
    {
        return $this->start === null;
    }
    /**
     * Returns TRUE if this range has a start and an end date
     * Example : from 2019-08-19 and until 2019-09-01
     *
     * @return boolean
     */
    public function isClosed()
    {
        return !$this->isLeftOpened() && !$this->isRightOpened();
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
    // overlap
    public function partiallyContainsRange($range)
    {
        if ($this->isLeftOpened() && 
            (
                !$range->isRightOpened() 
                && $this->containsDate($range->getEnd())
            )
        ) {
            return true;
        } 

        if ($this->isRightOpened() && 
            (
                !$range->isLeftOpened() 
                && $this->containsDate($range->getStart())
            )
        ) {
            return true;
        }
        
        if ( $this->isClosed() && $range->isClosed()) {
            return $this->containsDate($range->getStart()) && $this->containsDate($range->getEnd());
        } else {
            return false;
        }
    }

    public function fullyContainsRange($range)
    {
        //TODO: check input arg
        if ($this->isLeftOpened() && 
            ( 
                (
                    $range->isClosed() 
                    &&  $this->containsDate($range->getStart()) 
                    &&  $this->containsDate($range->getEnd())
                ) 
                ||
                (
                    $range->isLeftOpened()
                    && !$this->containsDate($range->getEnd())
                )
            )
        ) {
            return true;
        }

        if ($this->isRightOpened() &&
            (
                (
                    $range->isClosed() 
                    &&  $this->containsDate($range->getStart()) 
                    &&  $this->containsDate($range->getEnd())
                )
                ||
                (

                )
            )
        ) {
            return true;
        }



    }
    public function add($range)
    {

    }

    public function substract($range)
    {

    }
}