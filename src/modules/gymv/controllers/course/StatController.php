<?php

namespace app\modules\gymv\controllers\course;

use Yii;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\Order;
use \app\models\Product;
use \app\models\Category;
use \app\modules\gymv\models\MemberQuery;
use yii\db\Query;
use app\modules\gymv\models\ProductCourseQuery;
use app\modules\gymv\models\ProductCourse;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use \app\components\SessionDateRange;
use yii\helpers\ArrayHelper;
use app\components\ModelRegistry;

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

    /**
     * Display a list of courses with the number of participant for each course.
     * The view includes a filter on course categories
     *
     * @param [string] $category_filter comma separated list of categories to filter view
     * @return void
     */
    public function actionMemberCount($category_filter = null)
    {
        // create the category filter : no filter = all configured course categories
        $categoryIdFilter = empty($category_filter) 
            ? Yii::$app->params['courses_category_ids']
            : explode(',',$category_filter);

        // query DB
        $queryProduct = Product::find()
            ->select([
                '{{product}}.id',
                '{{product}}.name',
                '{{product}}.short_description',
                'COUNT(o.id) as order_count'
            ])
            ->where(['in', 'category_id', $categoryIdFilter])
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

            // build list of category option for the filter
            $categoriesResults = Category::find()
                ->select('id,name')
                ->where(['in', 'id', Yii::$app->params['courses_category_ids']])
                ->all();

            $categoryOptions = array_map(function($item) {
                return [ 'id' => $item->id, 'name' => $item->name];
            },$categoriesResults);
            
            // render
            return $this->render('member-count', [
                'dataProvider'    => $dataProvider,
                'categoryOptions' => $categoryOptions,
                'category_filter' => $category_filter
            ]);                    
        }

    }
}
