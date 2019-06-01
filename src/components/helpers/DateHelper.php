<?php

namespace app\components\helpers;

class DateHelper {
    /**
     * Returns the Age from Date of Birth
     *
     * @param string    $birthday format is YYYY-MM-DD
     * @return int
     */
    static public function computeAge($birthday)
    {
        // @see : https://thisinterestsme.com/php-calculate-age-date-of-birth/
        $dob = new \DateTime($birthday);
        $now = new \DateTime();
         
        //Calculate the time difference between the two dates.
        $difference = $now->diff($dob);
         
        return $difference->y;
    }
}