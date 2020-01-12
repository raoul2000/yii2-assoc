<?php

namespace app\modules\gymv\controllers\course;

use Yii;
use yii\web\NotFoundHttpException;
use \app\models\Contact;
use \app\models\OrderSearch;
use \app\models\Product;
use \app\models\Order;
use \app\components\SessionDateRange;
use app\modules\gymv\models\ProductSelectionForm;
use app\modules\gymv\models\ProductCourseSearch;
use app\modules\gymv\models\ProductCourseQuery;
use yii\helpers\ArrayHelper;
use \app\modules\gymv\models\QueryFactory;
use yii\data\ActiveDataProvider;

class HomeController extends \yii\web\Controller
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
    
    public function actionIndex()
    {
        // map<product_id, product_name> for courses - used in the dropdown (selectize) 
        // course selector (search)
        $products = ArrayHelper::map(
            Product::find()
                ->select(['id','name'])
                ->where(['in', 'id', ProductCourseQuery::allIds()])
                ->all(),
            'id',
            'name'
        );

        $searchModel = new OrderSearch();
        $searchModel->product_id = '';
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            QueryFactory::findCourseSold()
                ->with(['toContact'])
                ->orderBy('product_id')
        );

        $selectedProduct = null;
        if( !empty($searchModel->product_id)) {
            $selectedProduct = Product::findOne($searchModel->product_id);
            // product selected : display members without pagination
            // otherwise use default pagination to not having to
            // display too many members in one single page
            $dataProvider->setPagination(false);
        } 

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'products'     => $products,
            'selectedProduct' => $selectedProduct
        ]);
    }

    // NOT USED
    public function actionOverview()
    {
        $courseProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE);
        $orders = Order::find()
            ->select(['product_id', 'COUNT(*) as count_total'])
            ->with('product')
            ->where(['in', 'product_id', ProductCourseQuery::allIds()])
            ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
            ->groupBy('product_id')
            ->asArray()
            ->all();

        return $this->render('overview', [
            'orders' => $orders
        ]);            
    }

    // NOT USED
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
                    ->andWhereValidInDateRange(
                        SessionDateRange::getStart(), 
                        SessionDateRange::getEnd(),
                        'o.valid_date_start',
                        'o.valid_date_end'
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
        

        return $this->render('member-count', [
            // 'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'selectedProduct' => null
        ]);            
    }

    // NOT USED
    public function actionTest()
    {
        $searchModel = new ProductCourseSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams
        );
        $dataProvider->query->with('ordersAggregation');

        return $this->render('test', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
        ]);            
    }
}
