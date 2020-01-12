<?php

namespace app\modules\gymv\models;

use Yii;
use \app\models\Contact;

class MemberQuery extends \app\models\ContactQuery
{
    public function init()
    {
        $this->andOnCondition(['is_natural_person' => true]);
        parent::init();
    }

    /**
     * Returns a query selecting id of all contact considered as members for the current period.
     * A contact is a member if : 
     * 
     * - it is a natural person
     * - it as orders for at least one membership product 
     * - this order is valid for the current date range
     *
     * @return \yii\db\ActiveQuery
     */
    static public function allIds()
    {
        return Contact::find()
            ->select('c.id')
            ->from(['c' => Contact::tableName()])
            ->where(['c.is_natural_person' => true])
            ->innerJoinWith([
                'toOrders' => function($query) {
                    $query
                        ->andOnCondition(['in', 'product_id', Yii::$app->params['products_membership_ids']])
                        ->andOnCondition(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange());
                }
            ]);        
    }    
}
