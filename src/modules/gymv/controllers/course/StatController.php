<?php

namespace app\modules\gymv\controllers\course;

use Yii;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\Order;
use \app\models\Product;
use \app\modules\gymv\models\MemberQuery;
use yii\db\Query;
use app\modules\gymv\models\ProductCourseQuery;
use app\modules\gymv\models\ProductCourse;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use \app\components\SessionDateRange;

class StatController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
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
    public function actionIndex()
    {
        return $this->render('index');    
    }    

    public function actionMemberCount()
    {
        $queryProduct = Product::find()
            ->select([
                '{{product}}.id',
                '{{product}}.name',
                '{{product}}.short_description',
                'COUNT(o.id) as order_count'
            ])
            ->where(['in', 'category_id', Yii::$app->params['courses_category_ids']])
            ->joinWith(['orders' => function($query) {
                $query
                    ->from(['o' => Order::tableName()])
                    ->andOnCondition(
                        \app\components\helpers\DateRangeHelper::buildConditionOnDateRange(
                            [],
                            'O.valid_date_start',
                            'O.valid_date_end'    
                        )
                    );
            }])
            ->orderBy('order_count')
            ->groupBy('{{product}}.id')
            ->asArray();
/*
        $searchModel = new ProductCourseSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $queryProduct
        );
*/        
        $dataProvider = new ActiveDataProvider([
            'query' => $queryProduct,
        ]);

        if (\app\components\widgets\DownloadDataGrid::isDownloadRequest()) {
            $exporter = new \yii2tech\csvgrid\CsvGrid(
                [
                    'dataProvider' => new \yii\data\ActiveDataProvider([
                        'query' => $dataProvider->query,
                        'pagination' => [
                            'pageSize' => 100, // export batch size
                        ],
                    ]),
                    'columns' => [
                        ['attribute' => 'name'],
                        ['attribute' => 'order_count', 'label' => 'nombre de personne']
                    ]
                ]
            );
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            return $exporter->export()->send('course-count.csv');
        } else {
            return $this->render('member-count', [
                // 'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
                'selectedProduct' => null
            ]);                    
        }

    }
}
