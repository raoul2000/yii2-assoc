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
        $members = Contact::find()
            ->select('contact.id')
            ->where(['is_natural_person' => true])
            ->joinWith('toOrders o')
            ->andWhere(['in', 'o.product_id', [52,53]])
            ->asArray()
            ->all();

        $memberIds = array_map(function($contact) {
            return $contact['id']; //return $contact->id;
        }, $members);

        $membersCount = count($memberIds);


        // Order consmued belonging to 
        $courseProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE);
        $productRows = Product::find()
            ->select(['id','name'])
            ->where(['in' , 'id', $courseProductIds])
            ->all();

        $products = ArrayHelper::map($productRows, 'id', 'name');

        // TODO: query below does not take into account refund. If a course has been refunded to the
        // contact, then it will still appear as owned by the contact
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            Order::find()
                ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                ->with(['product', 'toContact'])
                ->where(['in', 'product_id', $courseProductIds] )
                ->andWhere(['in', 'to_contact_id', $memberIds])
                ->orderBy('product_id')
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'products' => $products
        ]);
    }

    public function actionOverview()
    {
        $courseProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE);
        $orders = Order::find()
            ->select(['product_id', 'COUNT(*) as count_total'])
            ->with('product')
            ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
            ->where(['in', 'product_id', $courseProductIds])
            ->groupBy('product_id')
            ->asArray()
            ->all();

        return $this->render('overview', [
            'orders' => $orders
        ]);            
    }
}
