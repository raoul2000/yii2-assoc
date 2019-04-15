<?php

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Constant;

class DateRangeWidget extends Widget
{
    /**
    * (non-PHPdoc)
    * @see \yii\base\Widget::run()
    */
    public function run()
    {
        $session = Yii::$app->session;
        if ($session->has(Constant::SESS_PARAM_NAME_DATERANGE)) {
            $range = $session->get(Constant::SESS_PARAM_NAME_DATERANGE);
            $label = Html::encode(
                'Clear Range ('
                . $range[Constant::SESS_PARAM_NAME_STARTDATE]
                . ' '
                . $range[Constant::SESS_PARAM_NAME_ENDDATE]
                . ')'
            ) ;
            $html = Html::a($label, ['delete-date-range', 'redirect_url' => Url::current() ], ['class' => 'btn btn-default']);
        } else {
            $html = Html::a('Date Range', ['create-date-range', 'redirect_url' => Url::current()], ['class' => 'btn btn-default']);
        }
        return $html;
    }
}
