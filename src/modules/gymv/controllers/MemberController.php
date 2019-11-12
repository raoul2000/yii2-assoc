<?php

namespace app\modules\gymv\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\Product;
use \app\models\Order;
use \app\models\BankAccount;
use \app\components\SessionDateRange;
use \app\components\SessionContact;
use app\modules\gymv\models\ProductSelectionForm;

class MemberController extends \yii\web\Controller
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
        $searchModel = new ContactSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams
        );

        $dataProvider
            ->query
            ->where(['is_natural_person' => true])
            ->joinWith('toOrders o')
            ->andWhere(['in', 'o.product_id', [52,53]]);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
        ]);    
    }

}
