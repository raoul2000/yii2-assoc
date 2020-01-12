<?php

namespace app\modules\gymv\models;

use Yii;
use \app\models\Product;

class ProductCourseQuery extends \app\models\ProductQuery
{
    public function init()
    {
        $this->andOnCondition(['in', 'category_id', Yii::$app->params['courses_category_ids']]);
        parent::init();
    }    

    /**
     * Returns a query selecting Ids for all products considered as courses.
     * A product is considered as a course if it belongs to one of the configured
     * categories.
     *
     * @return \yii\db\ActiveQuery
     */
    static public function allIds()
    {
        return Product::find()
            ->select('id')
            ->from(['p' => Product::tableName()])
            ->where(['in', 'p.category_id', Yii::$app->params['courses_category_ids']]);
    }    
}
