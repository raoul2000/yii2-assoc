<?php

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Constant;
use \app\components\SessionVars;

class DateRangeWidget extends Widget
{
    /**
    * (non-PHPdoc)
    * @see \yii\base\Widget::run()
    */
    public function run()
    {
        $dateRange = SessionVars::getDateRange();
        if ($dateRange) {
            $label = Html::encode(
                'Clear Range ('
                . $dateRange[0]
                . ' '
                . $dateRange[1]
                . ')'
            );
            $html = Html::a(
                $label,
                ['date-range', 'redirect_url' => Url::current(), 'clear' => '1'],
                ['class' => 'btn btn-default']
            );
        } else {
            $html = Html::a(
                'Date Range',
                ['date-range', 'redirect_url' => Url::current()],
                ['class' => 'btn btn-default']
            );
        }
        return $html;
    }
}
