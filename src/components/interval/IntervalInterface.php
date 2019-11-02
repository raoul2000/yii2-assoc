<?php

namespace \app\components\interval;

interface IntervalInterface {
    /**
     * Returns the start value for this interval or NULL if this
     * interval is left opened
     *
     * @return any
     */
    public function getStart() : IntervalValueInterface;
    /**
     * Returns the end value for this interval or NULL if this
     * interval is right opened
     *
     * @return any
     */
    public function getEnd() : IntervalValueInterface;
    /**
     * Return TRUE if this interval is LEFT opened, otherwise it returns FALSE.
     * An interval is LEFT opened if its start value is NULL.
     *
     * @return boolean
     */
    public function isLeftOpened(): boolean;    
    /**
     * Return TRUE if this interval is RIGHT opened, otherwose it returns FALSE.
     * An interval is RIGHT opened if its end value is NULL.
     *
     * @return boolean
     */
    public function isRightOpened(): boolean;
    /**
     * Returns TRUE if this interval is closed, otherwise it returns FALSE.
     * An interval is closed if it is neither LEFT nor RIGHT opened
     *
     * @return boolean
     */
    public function isClosed(): boolean;
    /**
     * Returns TRUE if this interval is opened, otherwise it returns FALSE.
     * An interval is opened if it is LEFT and RIGHT opened. There is only one
     * opened interval : [NULL, NULL]
     *
     * @return boolean
     */
    public function isOpened(): boolean;
}