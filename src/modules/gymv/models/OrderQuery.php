<?php

namespace app\modules\gymv\models;

use Yii;
use \app\components\helpers\ConverterHelper;
use \app\components\SessionDateRange;

class OrderQuery extends \app\models\OrderQuery
{
    public function membership()
    {
        $productIdsAsString = Yii::$app->configManager->getItemValue('product.consumed.by.registered.contact');
        $productIds = ConverterHelper::explode(',',$productIdsAsString);

        $validDateRangeCondition = $this->buildConditionOnDateRange(
            SessionDateRange::getStart(), 
            SessionDateRange::getEnd()
        );

        return $this
            ->andOnCondition(['in', 'product_id', $productIds])
            ->andOnCondition($validDateRangeCondition);
    }
}
