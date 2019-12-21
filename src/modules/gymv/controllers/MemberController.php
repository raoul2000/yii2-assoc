<?php

namespace app\modules\gymv\controllers;

use Yii;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\Order;
use \app\components\SessionDateRange;
use \app\components\helpers\ConverterHelper;
use \app\modules\gymv\models\MemberSearch;

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
        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams
        );
        
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
        ]);    
    }

}
