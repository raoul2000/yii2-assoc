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
}
