<?php

namespace \app\components\interval\wip;

class IntervalValidator {

    private $comparator;
    
    function __construct($intervalValueComparator) 
    {
        $this->comparator = $intervalValueComparator;
    }
    /**
     * Validate the interval defined by $start and $end values
     *
     * @param [type] $start
     * @param [type] $end
     * @return void
     */
    public function validateInterval($start, $end)
    {
        // validate types : $start, $end
        // validate $start <= $end
        if ($this->comparator->compare($start, $end) === -1) {
            throw new \Exception('start must be lower or equal to end');
        }
        return true;
    }
}
