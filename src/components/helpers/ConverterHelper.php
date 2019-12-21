<?php

namespace app\components\helpers;

use yii\base\InvalidCallException;

class ConverterHelper {

    /**
     * Apply the explode function removing empty items from the resulting table
     *
     * @param string $delimiter
     * @param string $value
     * @return array
     */
    public static function explode($delimiter, $value) 
    {
        return array_filter(
            array_map(function($item) {
                return trim($item);
            }, explode($delimiter, $value)), 
            function($item) {
                return !empty($item);
            }
        );
    }
}