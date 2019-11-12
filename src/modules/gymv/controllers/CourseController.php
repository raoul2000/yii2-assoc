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

        $searchModel = new OrderSearch();

        // TODO: query below does not take into account refund. If a course has been refunded to the
        // contact, then it will still appear as owned by the contact

        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            Order::find()
                ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                ->where(['in', 'product_id', $courseProductIds] )
                ->andWhere(['in', 'to_contact_id', $memberIds])
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}
