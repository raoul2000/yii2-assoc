<?php

namespace app\components\behaviors;

use Yii;
use yii\base\Behavior;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    public function init()
    {
        parent::init();
        $this->value = new \yii\db\Expression('NOW()');
    }
}
