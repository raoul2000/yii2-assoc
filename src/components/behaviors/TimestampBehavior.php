<?php

namespace app\components\behaviors;

use Yii;
use yii\base\Behavior;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    //public $value =  new \yii\db\Expression('NOW()');
    public function init()
    {
        $this->value = new \yii\db\Expression('NOW()');

        parent::init();
    }
}
