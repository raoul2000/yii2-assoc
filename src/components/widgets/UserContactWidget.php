<?php

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Constant;
use app\components\SessionContact;

class UserContactWidget extends Widget
{
    /**
    * (non-PHPdoc)
    * @see \yii\base\Widget::run()
    */
    public function run()
    {

        $session = Yii::$app->session;
        if (SessionContact::getContactId() != null) {
            $label = Html::encode(
                'Clear User Contact (' . Html::encode(SessionContact::getContactName()) . ')'
            ) ;
            $html = Html::a(
                $label, 
                ['user-contact', 'redirect_url' => Url::current(), 'clear' => true ], 
                ['class' => 'btn btn-default']
            );
        } else {
            $html = Html::a(
                'User Contact', 
                ['user-contact', 'redirect_url' => Url::current()], 
                ['class' => 'btn btn-default']
            );
        }
        return $html;
    }
}
