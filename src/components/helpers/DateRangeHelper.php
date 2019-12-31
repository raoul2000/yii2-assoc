<?php

namespace app\components\helpers;

use Yii;
use app\components\Constant;
use yii\helpers\Html;
use \app\components\helpers\DateHelper;
use yii\base\InvalidCallException;
use \app\components\SessionDateRange;

class DateRangeHelper
{
    /**
     * Create the HTML markup to render the current Date Range i_n the site top navbar
     *
     * @param [type] $redirect_url
     * @return string
     */
    public static function buildMenuItem($redirect_url)
    {
        $dateRange = SessionDateRange::getDateRange();

        if (!$dateRange) {
            // no date range current
            return [
                'label'   => '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>',
                'options' => ['title' => 'Select a Date Range'],
                'encode'  => false,
                'url'     => ['/admin/home/date-range', 'redirect_url' => $redirect_url]
            ];
        } else {

            $startStr = DateHelper::toDateAppFormat($dateRange->start);
            $endStr   = DateHelper::toDateAppFormat($dateRange->end);

            $label = !empty($dateRange->id)
                ? Html::encode($dateRange->id)
                : $startStr . ' - ' . $endStr;

            $titleText = $startStr . ' to ' . $endStr;
            return [
                'label'   =>  '<span class="label label-primary" style="font-size:1em">' 
                        . '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> '
                        . $label
                    . '</span>',
                'encode'  => false,
                'options' => ['title' => $titleText],
                'url'     => ['/admin/home/date-range', 'redirect_url' => $redirect_url]
            ];
        }
    }
    /**
     * WIP
     *
     * @param [type] $startDate
     * @param [type] $endDate
     * @return void
     */
    public static function inCurrentDateRange($startDate, $endDate)
    {
        // TODO: to implement (maybe)
        $range = SessionDateRange::getDateRange();
        if ($range === null) {
            return true;
        }
        if (!empty($range->start) && !empty($range->end)) {

        }
    }

    /**
     * Evaluate a configured date range value.
     * Configured range values may be provided as string or as anonymous functions. This method detects
     * the actual type of the passed argument and returns the corresponding value.
     *
     * @param string|closure $arg
     * @return string
     */
    public static function evaluateConfiguredRangeValue($arg)
    {
        if (is_string($arg)) {
            // string value returned unchanged
            return $arg;
        } else {
            $reflection = new \ReflectionFunction($arg);
            if ($reflection->isClosure()) {
                // we have an anonymous function : evaluate it and return its results that is
                // expected to be a string
                return $arg();
            } else {
                throw new Exception('date range configured is neither a string nor a function');
            }
        }
    }
    /**
     * Create an SQL condition that is TRUE when the reference_date column of a record
     * is included in the given date Range.
     * 
     * The first argument is the date range to test. By default it is set to an empty array which is interpreted
     * as the current session date range. Otherwise, it should contain [$startDate; $endDate]. If one of them is NULL
     * then we are dealing with an opened range (left or right opened).
     *
     * @param array $dateRange the date range to test
     * @param string $colName default 'reference_date' the name of the date column to apply condition on
     * @return array the SQL condition
     */
    public function buildConditionOndateInRange($dateRange = [], $colName = 'reference_date')
    {
        list($startDate, $endDate) = self::resolveDateRange($dateRange);
        self::validateDateRangeValues($startDate, $endDate);

        // create conditions
        $conditions = [];
        $NULL = new \yii\db\Expression('null');
        if (!empty($startDate) && !empty($endDate)) {
            $conditions = [
                'OR',
                ['IS', $colName, $NULL],
                ['BETWEEN', $colName, $startDate, $endDate]
            ];
        } elseif (!empty($startDate)) {
            $conditions = [
                'OR',
                ['IS', $colName, $NULL],
                ['>=', $colName, $startDate],
            ];
        } elseif (!empty($endDate)) {
            $conditions = [
                'OR',
                ['IS', $colName, $NULL],
                ['<=', $colName, $endDate],
            ];
        }
        return $conditions;
    }

    /**
     * Converts the given date range into an effective date range.
     *
     * @param [type] $dateRange
     * @return array effective date range
     */
    public static function resolveDateRange($dateRange = [])
    {
        if(!is_array($dateRange)) {
            throw new yii\web\ServerErrorHttpException('invalid argument type : array expected');
        } else {
            switch(count($dateRange)) {
                case 0 : $dateRange = [SessionDateRange::getStart(), SessionDateRange::getEnd()];
                break;
                case 1 : $dateRange[] = SessionDateRange::getEnd();
                break;
            }
        }
        return $dateRange;
    }

    /**
     * Validate the provided date range.
     * If it is a closed range, then start date must NOT be after end date. If that's not the case, throw an exception
     *
     * @param string $startDate format yyyy-mm-dd the start date range, or empty if left opened range
     * @param string $endDate format yyyy-mm-dd the start date range, or empty if right opened range
     * @return void
     */
    public static function validateDateRange($startDate, $endDate)
    {
        if (!empty($startDate) && !empty($endDate) && \strtotime($startDate) > strtotime($endDate)) {
            throw new \yii\base\InvalidCallException('start date range must be lower or equal to end date : start = '
                . $startDate . ' end = ' . $endDate);
        }
    }

    /**
     * Create an SQL condition that is TRUE when a date range is partially ot fully included in 
     * the date range of a DB record.
     * The first argument is the date range to test. By default it is set to an empty array which is interpreted
     * as the current session date range. Otherwise, it should contain [$startDate; $endDate]. If one of them is NULL
     * then we are dealing with an opened range (left or right opened).
     * 
     * @param array $dateRange and array defining the date range
     * @param string $startFieldName the start date range column name
     * @param string $endFieldName the end date range column name
     * @return array the SQL condition
     */
    public static function buildConditionOnDateRange($dateRange = [], 
        $startFieldName = 'valid_date_start', $endFieldName = 'valid_date_end')
    {
        list($startDate, $endDate) = self::resolveDateRange($dateRange);
        self::validateDateRange($startDate, $endDate);

        // create conditions
        $conditions = [];
        $NULL = new \yii\db\Expression('null');

        if (!empty($startDate) && !empty($endDate)) {
            /**
             * List of valid dante range configurations
             * ---------------------------------------
             * 
             * B = valid_date_start (Begin)
             * E = valid_date_end   (End)
             * 
             *       $startDate   $endDate
             * ----------|------------|--------------
             *     B     :            :
             *     B     :     E      :       
             *     B     :            :       E
             *           :    B       :       
             *           :    B E     :       
             *           :    B       :       E
             *           :    E       :       
             *           :            :       E
             *           :            :       
             */

            $conditions = [
                'AND',
                ['OR',
                    ['IS', $startFieldName, $NULL],
                    ['<=', $startFieldName, $endDate],
                ],
                ['OR',
                    ['IS', $endFieldName, $NULL],
                    ['>=', $endFieldName, $startDate]
                ]
            ];
        } elseif (!empty($startDate)) { // only start date (valid from date ....)
            /**
             * List of valid dante range configurations
             * ---------------------------------------
             * 
             * B = valid_date_start (Begin)
             * E = valid_date_end   (End)
             * 
             *       $startDate   
             * ----------|--------------------------
             *     B     :            
             *     B     :     E             
             *           :     E
             *           :                   
             */     
            $conditions = [
                'AND',
                ['OR',
                    ['IS', $startFieldName, $NULL],
                    ['<=', $startFieldName, $startDate],
                ],
                ['OR',
                    ['IS', $endFieldName, $NULL],
                    ['>=', $endFieldName, $startDate]
                ]
            ];
        } elseif (!empty($endDate)) { // only end date (valid until date ... )
            /**
             * List of valid dante range configurations
             * ---------------------------------------
             * 
             * B = valid_date_start (Begin)
             * E = valid_date_end   (End)
             * 
             *                    $endDate   
             * ----------------------|------------
             *     B                 :            
             *     B                 :     E             
             *                       :     E
             *                       :                   
             */           
            $conditions = [
                'AND',
                ['OR',
                    ['IS', $startFieldName, $NULL],
                    ['<=', $startFieldName, $endDate],
                ],
                ['OR',
                    ['IS', $endFieldName, $NULL],
                    ['>=', $endFieldName, $endDate]
                ]
            ];               
        }
        return $conditions;
    }

}
