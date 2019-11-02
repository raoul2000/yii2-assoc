<?php

namespace \app\components\interval;

interface IntervalComparatorInterface {
    /**
     * Compare $a with $b and returns the result of this comparaison.
     * 
     * returned values : 
     * - 1 : if $a is greater then $b
     * - 0 : if $a equals $b
     * - -1 : if $a is lower than $b
     *
     * @param IntervalInterface $a
     * @param IntervalInterface $b
     * @return void
     */
    public function compare(IntervalInterface $a, IntervalInterface $b) : int;
}