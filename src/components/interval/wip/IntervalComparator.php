<?php
// https://fr.wikipedia.org/wiki/Alg%C3%A8bre_des_intervalles_d%27Allen

namespace \app\components\interval\wip;

class IntervalComparator {

    private $cmp;

    function __construct(IntervalValueComparator $valueComparator) 
    {
        $this->cmp = $valueComparator;
    }

    /**
     * x : ----|
     * y :        |------
     *
     * @param Interval $x
     * @param Interval $y
     * @return boolean
     */
    function before(Interval $x, Interval $y) : boolean
    {
        if($x->getEnd() !== null && $y->start() !== null) {
            return $this->cmp($x->getEnd(), $y->start()) === -1;
        }
        return false;
    }
    /**
     * x :        |------
     * y : ----|
     *
     * @param Interval $x
     * @param Interval $y
     * @return boolean
     */
    function after(Interval $x, Interval $y) : boolean
    {
        return $this->before($y, $x);
    }

    /**
     * x : ------|
     * y :       |------
     *
     * @param Interval $x
     * @param Interval $y
     * @return boolean
     */
    function meets(Interval $x, Interval $y) : boolean
    {
        if($x->getEnd() !== null && $y->start() !== null) {
            return $this->cmp($x->getEnd(), $y->start()) === 0;
        }
        return false;
    }
    /**
     * x :       |------
     * y : ------|
     * 
     * @param Interval $x
     * @param Interval $y
     * @return boolean
     */
    function imeets(Interval $x, Interval $y) : boolean
    {
        return $this->meets($y, $x);
    }

    /**
     * x : --------|
     * y :      |--------
     * 
     * @param Interval $x
     * @param Interval $y
     * @return boolean
     */
    function overlaps(Interval $x, Interval $y) : boolean
    {
        if($x->getEnd() !== null && $y->start() !== null) {
            return $this->cmp($x->getEnd(), $y->start()) === 1;
        }
        return false;
    }

    /**
     * x :      |--------
     * y : --------|
     * 
     * @param Interval $x
     * @param Interval $y
     * @return boolean
     */
    function ioverlaps(Interval $x, Interval $y) : boolean
    {
        return $this->overlaps($y, $x);
    }

    /**
     * x : |--------
     * y : |------------
     * 
     * @param Interval $x
     * @param Interval $y
     * @return boolean
     */
    function starts(Interval $x, Interval $y) : boolean
    {
        if($x->getStart() !== null && $y->start() !== null) {
            return $this->cmp($x->getStart(), $y->start()) === 0;
        }
        return false;
    }
}
