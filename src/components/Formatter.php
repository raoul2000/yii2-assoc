<?php

namespace app\components;

class Formatter extends \yii\i18n\Formatter 
{
    public function asGender($value)
    {
        switch($value) {
            case 1 : return \Yii::t('app', 'male');
            case 2 : return \Yii::t('app', 'female');
            case 0 : return \Yii::t('app', 'undefined');
        }
    }
}
