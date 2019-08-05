<?php

namespace app\components\helpers;

use yii\base\InvalidCallException;

class DateHelper
{
    /**
     * Returns the Age from Date of Birth
     *
     * @param string $birthday format is YYYY-MM-DD
     * @return int
     */
    public static function computeAge($birthday)
    {
        // @see : https://thisinterestsme.com/php-calculate-age-date-of-birth/
        $dob = new \DateTime($birthday);
        $now = new \DateTime();
         
        //Calculate the time difference between the two dates.
        $difference = $now->diff($dob);
         
        return $difference->y;
    }

    /**
     * Converts a Date in DB format, into a App Format.
     * DB Format : YYYY-MM-DD
     * App format : DD/MM/YYYY
     *
     * @param string $value
     * @return void
     */
    public static function toDateAppFormat($value)
    {
        $arr = explode('-', $value);
        if (count($arr) !== 3) {
            throw new InvalidCallException('The date value has not the expected format (' . $value . ')');
        }
        //$arr = preg_split('/(-| |\/)/', $this->birthday);
        return $arr[2] . '/' . $arr[1] . '/' . $arr[0];
    }

    /**
     * Convert a Date in App format, into a DB Format.
     * App format : DD/MM/YYYY
     * DB Format : YYYY-MM-DD
     *
     * This conversion is the opposite conversion of toDateAppFormat
     * @param string $value
     * @return void
     */
    public static function toDateDbFormat($value)
    {
        // input date format : dd/mm/yyyy
        $arr = explode('/', $value);
         //$arr = preg_split('/(-| |\/)/', $this->birthday);
        if (count($arr) !== 3) {
            throw new InvalidCallException('The date value has not the expected format (' . $value . ')');
        }
        
        return $arr[2] . '-' . $arr[1] . '-' . $arr[0];
    }
}