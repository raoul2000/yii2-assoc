<?php

namespace app\modules\gymv\controllers\member;

use Yii;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\Order;
use \app\models\Product;
use \app\modules\gymv\models\MemberQuery;
use yii\db\Query;
use app\modules\gymv\models\ProductCourseQuery;
use yii\web\NotFoundHttpException;

class StatController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' =>  \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }    
    public function actionIndex($dataSet = 'all')
    {
        return $this->render('index');    
    }    

    protected function getQueryMembersIdPeriod1()
    {
        return Contact::find()
            ->select('c.id')
            ->from(['c' => Contact::tableName()])
            ->where(['c.is_natural_person' => true])
            ->innerJoinWith([
                'toOrders' => function($query) {
                    $query
                        ->andOnCondition(['in', 'product_id', Yii::$app->params['products_membership_ids']])
                        ->andOnCondition(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange(
                            ['2018-09-1', '2019-08-30']
                        ));
                }
            ]);        
    }

    public function actionDiffPeriod1()
    {
        $query = Contact::find()
            ->from(['c' => Contact::tableName()])
            ->where(['in', 'c.id', $this->getQueryMembersIdPeriod1()])
            ->andWhere(['not in', 'c.id', MemberQuery::allIds()]);

        $searchModel = new ContactSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $query
        );

        if (\app\components\widgets\DownloadDataGrid::isDownloadRequest()) {
            $exporter = new \yii2tech\csvgrid\CsvGrid(
                [
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['attribute' => 'name',  'label' => \Yii::t('app', 'Name')],
                        ['attribute' => 'firstname', 'label' => \Yii::t('app', 'Firstname')],
                        ['attribute' => 'gender'],
                        ['attribute' => 'email'],
                        ['attribute' => 'birthday'],
                    ]
                ]
            );
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            return $exporter->export()->send('contacts.csv');

        } else {
            return $this->render('diff-period1', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
            ]);        
        }
    }

    public function actionCoursePurchased()
    {
        $qryAggregateCoursePuchased = Contact::find()
            ->from(['c' => Contact::tableName()])
            ->select([
                'c.id',
                'COUNT(o.id) as order_count'
            ])        
            ->where(['in', 'c.id', MemberQuery::allIds()])
            ->innerJoinWith([
                'toOrders' => function($q) {
                    $q
                        ->from(['o' => Order::tableName()])
                        ->andOnCondition(['in', 'o.product_id', ProductCourseQuery::allIds()])
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
