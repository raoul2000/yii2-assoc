<?php

namespace \app\components\interval;

interface IntervalValueComparatorInterface {
    /**
     * Compare $a with $b and returns the result of this comparaison.
     * 
     * returned values : 
     * - 1 : if $a is greater then $b
     * - 0 : if $a equals $b
     * - -1 : if $a is lower than $b
     *
     * @param IntervalValueInterface $a
     * @param IntervalValueInterface $b
     * @return void
     */
    public function compare(IntervalValueInterface $a, IntervalValueInterface $b) : int;
    public function min(IntervalValueInterface $a, IntervalValueInterface $b) : IntervalValueInterface;
    public function max(IntervalValueInterface $a, IntervalValueInterface $b) : IntervalValueInterface;
}
