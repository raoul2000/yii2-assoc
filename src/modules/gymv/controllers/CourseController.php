<?php

namespace app\modules\gymv\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\OrderSearch;
use \app\models\Product;
use \app\models\Order;
use \app\components\SessionDateRange;
use app\modules\gymv\models\ProductSelectionForm;
use yii\helpers\ArrayHelper;
use \app\modules\gymv\models\QueryFactory;


class CourseController extends \yii\web\Controller
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
        // Order consmued belonging to 
        $courseProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE);
        
        // map<product_id, product_name> for courses - used in the dropdown (selectize) 
        // course selector (search)
        $products = ArrayHelper::map(
            Product::find()
                ->select(['id','name'])
                ->where(['in', 'id', $courseProductIds])
                ->all(),
            'id',
            'name'
        );

        $searchModel = new OrderSearch();
        $searchModel->product_id = '';
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            QueryFactory::findCourseSold($courseProductIds)
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

    public function actionOverview()
    {
        $courseProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE);
        $orders = Order::find()
            ->select(['product_id', 'COUNT(*) as count_total'])
            ->with('product')
            ->where(['in', 'product_id', $courseProductIds])
            ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
            ->groupBy('product_id')
            ->asArray()
            ->all();

        return $this->render('overview', [
            'orders' => $orders
        ]);            
    }
}
