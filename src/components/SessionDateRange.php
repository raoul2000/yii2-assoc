<?php

namespace app\components;

use Yii;
use app\components\Constant;
use yii\helpers\Html;
use \app\components\helpers\DateHelper;
use \app\components\helpers\DateRangeHelper;

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
        if (empty($start_date) && empty($end_date) && empty($rangeId)) {
            self::clearDateRange();
        } else {
            Yii::$app->session[self::DATE_RANGE] = [
                self::RANGE_ID    => $rangeId,
                self::RANGE_START => $start_date,
                self::RANGE_END   => $end_date,
            ];
        }
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
     * Load Default Date Range from config into Session.
     * The configured value must be the same as the configured date range key
     *
     * @return void
     */
    public static function loadFromConfig() {
        $conf = Yii::$app->configManager;
        $date_range_id = $conf->getItemValue('date_range_id');
        if (!empty($date_range_id)) {
            if (!array_key_exists($date_range_id, Yii::$app->params['dateRange'])) {
                Yii::error('failed to load date range with ID = ' . $date_range_id);
            } else {
                $range = Yii::$app->params['dateRange'][$date_range_id];

                SessionDateRange::setDateRange(
                    DateRangeHelper::evaluateConfiguredRangeValue($range['start']),
                    DateRangeHelper::evaluateConfiguredRangeValue($range['end']),
                    $date_range_id
                );
            }
        }
    }    
}
