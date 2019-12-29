<?php

namespace app\modules\gymv\models;

use Yii;
use \app\components\helpers\DateRangeHelper;

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
    public function getValidOrdersCount()
    {
        if ($this->isNewRecord) {
            return null; // this avoid calling a query searching for null primary keys
        }
        
        return empty($this->ordersAggregation) ? 0 : $this->ordersAggregation[0]['counted'];
    }    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersValidInDateRange()
    {
        /*
        $condition = $this->buildConditionOnDateRange(
            \app\components\SessionDateRange::getStart(),
            \app\components\SessionDateRange::getEnd()
        );*/
        
        $condition = DateRangeHelper::buildConditionOnDateRange();
        $query = $this->hasMany(Order::className(), ['product_id' => 'id']);
        if(!empty($condition)) {
            $query->where($condition);
        }
        return $query;
        /*
        return $this->hasMany(Order::className(), ['product_id' => 'id']);
        */
    }

    public function getOrdersAggregation()
    {
        return $this->getOrdersValidInDateRange()
            ->select(['product_id', 'counted' => 'count(*)'])
            ->groupBy('product_id')
            ->asArray(true);
    }    

    public function buildConditionOnDateRange($startDate, $endDate = null, 
        $startFieldName = 'valid_date_start', $endFieldName = 'valid_date_end')
    {
        // create conditions
        $conditions = [];
        $NULL = new \yii\db\Expression('null');

        if (!empty($startDate) && !empty($endDate)) {
            /**
             * List of valid dante range configurations
             * ---------------------------------------
             * 
             * B = valid_date_start (Begin)
             * E = valid_date_end   (End)
             * 
             *       $startDate   $endDate
             * ----------|------------|--------------
             *     B     :            :
             *     B     :     E      :       
             *     B     :            :       E
             *           :    B       :       
             *           :    B E     :       
             *           :    B       :       E
             *           :    E       :       
             *           :            :       E
             *           :            :       
             */

            $conditions = [
                'AND',
                ['OR',
                    ['IS', $startFieldName, $NULL],
                    ['<=', $startFieldName, $endDate],
                ],
                ['OR',
                    ['IS', $endFieldName, $NULL],
                    ['>=', $endFieldName, $startDate]
                ]
            ];
        } elseif (!empty($startDate)) { // only start date (valid from date ....)
            /**
             * List of valid dante range configurations
             * ---------------------------------------
             * 
             * B = valid_date_start (Begin)
             * E = valid_date_end   (End)
             * 
             *       $startDate   
             * ----------|--------------------------
             *     B     :            
             *     B     :     E             
             *           :     E
             *           :                   
             */     
            $conditions = [
                'AND',
                ['OR',
                    ['IS', $startFieldName, $NULL],
                    ['<=', $startFieldName, $startDate],
                ],
                ['OR',
                    ['IS', $endFieldName, $NULL],
                    ['>=', $endFieldName, $startDate]
                ]
            ];
        } elseif (!empty($endDate)) { // only end date (valid until date ... )
            /**
             * List of valid dante range configurations
             * ---------------------------------------
             * 
             * B = valid_date_start (Begin)
             * E = valid_date_end   (End)
             * 
             *                    $endDate   
             * ----------------------|------------
             *     B                 :            
             *     B                 :     E             
             *                       :     E
             *                       :                   
             */           
            $conditions = [
                'AND',
                ['OR',
                    ['IS', $startFieldName, $NULL],
                    ['<=', $startFieldName, $endDate],
                ],
                ['OR',
                    ['IS', $endFieldName, $NULL],
                    ['>=', $endFieldName, $endDate]
                ]
            ];               
        }
        return $conditions;
    }

}
