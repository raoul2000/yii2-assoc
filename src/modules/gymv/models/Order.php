<?php

namespace app\modules\gymv\models;

use Yii;

class Order extends \app\models\Order
{
    public static function find()
    {
        return new OrderQuery(get_called_class());
    }
}
