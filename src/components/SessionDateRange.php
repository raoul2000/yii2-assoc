<?php

namespace app\components;

use Yii;
use app\components\Constant;
use yii\helpers\Html;

/**
 * This is the persistence layer for the Date Range values that may
 * be set by the user.
 */
class SessionDateRange
{
    const DATE_RANGE  = 'date_range';
    const RANGE_START = 'start';
    const RANGE_END   = 'end';
    const RANGE_ID    = 'id';

    
    /**
     * Set the date range values in the current session
     *
     * @param string $start_date
     * @param string $end_date
     * @return void
     */
    public static function setDateRange($start_date, $end_date, $rangeId = null)
    {
        Yii::$app->session[self::DATE_RANGE] = [
            self::RANGE_ID    => $rangeId,
            self::RANGE_START => $start_date,
            self::RANGE_END   => $end_date,
        ];
    }

    /**
     * Returns the current date range info or NULL if not date range is defined.
     * The returned object has following structure :
     *
     * {
     *  "start" : yyyy-mm-dd, // the date start range
     *  "end" : yyyy-mm-dd, // the date end range
     *  "name" : string| null
     * }
     *
     * @return NULL|object
     */
    public static function getDateRange()
    {
        $session = Yii::$app->session;

        // no date range found in session : do nothing and return
        if (!$session->has(self::DATE_RANGE)) {
            return null;
        }
        $dateRange = $session[self::DATE_RANGE];
        return (object) $session[self::DATE_RANGE];
    }

    /**
     * Returns the current Range start date or NULL if no date range is defined
     *
     * @return void
     */
    public static function getStart()
    {
        $session = Yii::$app->session;
        if (!$session->has(self::DATE_RANGE)) {
            return null;
        }
        return $session[self::DATE_RANGE][self::RANGE_START];
    }

    /**
     * Returns the current Range start date or NULL if no date range is defined
     *
     * @return void
     */
    public static function getEnd()
    {
        $session = Yii::$app->session;
        if (!$session->has(self::DATE_RANGE)) {
            return null;
        }
        return $session[self::DATE_RANGE][self::RANGE_END];
    }
    /**
     * Remove the date range criteria from the session
     *
     * @return void
     */
    public static function clearDateRange()
    {
        Yii::$app->session->remove(self::DATE_RANGE);
    }

    /**
     * Modifies the provided object to apply date range criteria.
     * Depending on the $model type, the SQL condition is modified differently.
     *
     * @param mixed $queryOrDataprovider
     * @param object instance of yii\base\Model
     * @return mixed
     */
    public static function applyDateRange($queryOrDataprovider, $model)
    {
        if ($queryOrDataprovider instanceof \yii\data\ActiveDataProvider) {
            $query = $queryOrDataprovider->query;
        } else {
            $query = $queryOrDataprovider;
        }

        $session = Yii::$app->session;

        if (!$session->has(self::DATE_RANGE)) {
            return $queryOrDataprovider;
        }
        $range = self::getDateRange();
        // no date range found in session : do nothing and return
        if ($range == null) {
            return $queryOrDataprovider;
        }

        // apply date range criteria to model
        // depending on model's attributes, SQL condition is modified to apply
        // date range criteria
        $attributeNames = array_keys($model->getAttributes());
        if (in_array('reference_date', $attributeNames)) {
            $query->andWhere(['between', 'reference_date', $range->start, $range->end]);
        }
        return $queryOrDataprovider;
    }

    public static function getLabel()
    {
        $dateRange = self::getDateRange();

        if (!$dateRange) {
            return 'no date range';
        }

        if (!empty($dateRange->id)) {
            return $dateRange->id;
        } else {
            return $dateRange->start . ' - ' . $dateRange->end;
        }
    }

    public static function buildMenuItem($redirect_url)
    {
        $dateRange = self::getDateRange();
        if (!$dateRange) {
            return [
                'label' => '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>',
                'options' => ['title' => 'Select a Date Range'],
                'encode' => false,
                'url' => ['/admin/home/date-range', 'redirect_url' => $redirect_url]
            ];
        } else {
            $label = \app\components\SessionDateRange::getLabel();
            return [
                //'label' =>  $label,
                'label' =>  '<span class="label label-primary" style="font-size:1em">' 
                    . '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> '
                    . $label
                . '</span>',
                //'label' => '<button type="button" class="btn btn-default btn-xs">' . $label . '</button>',
                
                'encode' => false,
                'options' => ['title' => $dateRange->start . ' to ' . $dateRange->end ],
                'url' => ['/admin/home/date-range', 'redirect_url' => $redirect_url]
            ];
        }
    }
}
