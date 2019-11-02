<?php

namespace \app\components\interval;

/**
 * Represents the interface that must be implemented by object which are
 * used to create an Interval.
 */
interface IntervalValueInterface {
    public function getValue();
    public function setValue($value);
}