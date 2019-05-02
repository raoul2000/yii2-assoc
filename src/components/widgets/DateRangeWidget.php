<?php

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use \app\components\SessionDateRange;

class DateRangeWidget extends Widget
{
    /**
    * (non-PHPdoc)
    * @see \yii\base\Widget::run()
    */
    public function run()
    {
        $dateRange = SessionDateRange::getDateRange();
        if ($dateRange) {
            $label = Html::encode(
                'Update Range ('
                . $dateRange[0]
                . ' '
                . $dateRange[1]
                . ')'
            );
            $html = Html::a(
                $label,
                ['date-range', 'redirect_url' => Url::current()],
                ['class' => 'btn btn-default']
            );
        } else {
            $html = Html::a(
                'Set Date Range',
                ['date-range', 'redirect_url' => Url::current()],
                ['class' => 'btn btn-default']
            );
        }
        return $html;
    }
}
