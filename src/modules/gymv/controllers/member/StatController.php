<?php

namespace app\modules\gymv\controllers\member;

use Yii;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\OrderSearch;
use \app\models\Order;
use \app\models\Product;
use \app\components\SessionDateRange;
use \app\components\SessionContact;
use \app\components\helpers\ConverterHelper;
use \app\modules\gymv\models\MemberSearch;
use \app\modules\gymv\models\QueryFactory;
use app\modules\gymv\models\ProductSelectionForm;
use yii\db\Query;
use app\modules\gymv\models\ProductCourseSearch;
use yii\web\NotFoundHttpException;

class StatController extends \app\modules\gymv\controllers\member\HomeController
{
    public function actionIndex($dataSet = 'all')
    {
        return $this->render('index');    
    }    

    public function actionCoursePurchased()
    {
        $qryAggregateCoursePuchased = Contact::find()
            ->from(['c' => Contact::tableName()])
            ->select([
                'c.id',
                'COUNT(o.id) as order_count'
            ])        
            ->where(['in', 'c.id', $this->getQueryMembersId()])
            ->innerJoinWith([
                'toOrders' => function($q) {
                    $q
                        ->from(['o' => Order::tableName()])
                        ->andOnCondition(['in', 'o.product_id', $this->getQueryCourseIds()])
                        ->andOnCondition(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange());
                }
            ])
            ->groupBy('c.id')
            ->asArray();

        // aggregate on order_count column

        $query = (new yii\db\Query())
            ->select('order_count, count(order_count) as total')
            ->from(['aggrCoursePurchased' => $qryAggregateCoursePuchased])
            ->orderBy('total DESC')
            ->groupBy('order_count');

        $data = $query->all();

        // creat serie
        $serie = [
            'name' => 'adhérents',
            'colorByPoint' => true,
            'data' => []
        ];
        
        foreach ($data as $key => $value) {
            $serie['data'][] = [
                'name' => $value['order_count'] 
                    . ' '
                    . ( $value['order_count'] < 2 
                        ? 'cour acheté'
                        : 'cours achetés'),
                'y'    => intVal($value['total'])
            ];
        }
        return $this->render('course-purchased', [
            'data'  => $data,
            'serie' => $serie,
            'title' => '',
            'subTitle' => ''
        ]);            
    }
}
