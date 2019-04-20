<?php

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Constant;

class UserContactWidget extends Widget
{
    /**
    * (non-PHPdoc)
    * @see \yii\base\Widget::run()
    */
    public function run()
    {
        $session = Yii::$app->session;
        if ($session->has(Constant::SESS_PARAM_NAME_CONTACT)) {
            $label = Html::encode(
                'Clear User Contact (' . Html::encode($session[Constant::SESS_PARAM_NAME_CONTACT]['name']) . ')'
            ) ;
            $html = Html::a(
                $label, 
                ['create-user-contact', 'redirect_url' => Url::current(), 'clear' => true ], 
                ['class' => 'btn btn-default']
            );
        } else {
            $html = Html::a(
                'User Contact', 
                ['create-user-contact', 'redirect_url' => Url::current()], 
                ['class' => 'btn btn-default']
            );
        }
        return $html;
    }
}
