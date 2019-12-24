<?php

namespace app\modules\gymv\controllers;

use Yii;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\Order;
use \app\components\SessionDateRange;
use \app\components\helpers\ConverterHelper;
use \app\modules\gymv\models\MemberSearch;
use \app\modules\gymv\models\QueryFactory;

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
  /*      
        // read list of product ids identifying a registered contact (from config)
        $productIdsAsString = Yii::$app->configManager->getItemValue('product.consumed.by.registered.contact');
        $productIds = ConverterHelper::explode(',',$productIdsAsString);

        // search contact having valid order for those products : they are registered members
        $query = \app\modules\gymv\models\Member::find()
            ->joinWith([
                'toOrders' => function($q) use($productIds) {
                    $q
                        ->from(['o' => Order::tableName()])
                        ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                        ->andWhere(['in', 'o.product_id', $productIds]);
                }
            ]);   
            */

        $query = \app\modules\gymv\models\Member::find()
                ->joinWith('membershipOrders');
        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $query
        );
        
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
        ]);    
    }

}
