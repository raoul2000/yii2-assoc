<?php

namespace app\modules\gymv\models;

use Yii;

class Member extends \app\models\Contact
{
    public static function find()
    {
        return new MemberQuery(get_called_class());
    }

    public function getMembershipOrders()
    {
        return $this
            ->hasMany(Order::className(), ['to_contact_id' => 'id'])
            ->membership();
    }    

    /**
     * Returns all orders provided by this contact
     * @return \yii\db\ActiveQuery
     */
    /*
    public function getMembershipOrders()
    {
        $providerContactId = 5;
        return $this->hasMany(
                Order::className(), 
                ['to_contact_id'   => 'id']
            )
            ->andWhere(['from_contact_id' => $providerContactId])
            ->andWhere(['in', 'product_id', ]);
    }*/
}
