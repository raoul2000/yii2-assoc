<?php

namespace app\modules\gymv\models;

use Yii;

/**
 * This is the model class for table "Course".
 */
class ProductCourse extends \app\models\Product
{
    public $order_count;
    /**
     * {@inheritdoc}
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductCourseQuery(get_called_class());
    }   
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembershipOrders()
    {
        return $this->hasMany(Order::className(), ['product_id' => 'id'])
            ->where();
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersValidInDateRange()
    {
        return $this->hasMany(Order::className(), ['product_id' => 'id']);
    }    
}
