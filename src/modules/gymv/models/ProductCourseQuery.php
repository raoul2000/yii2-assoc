<?php

namespace app\modules\gymv\models;

use Yii;

class ProductCourseQuery extends \app\models\ProductQuery
{
    public function init()
    {
        $this->andOnCondition(['in', 'category_id', Yii::$app->params['courses_category_ids']]);
        parent::init();
    }    
}
