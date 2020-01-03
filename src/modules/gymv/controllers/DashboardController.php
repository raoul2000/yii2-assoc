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
use yii\helpers\Url;
use \app\modules\gymv\models\MemberSearch;
use \app\modules\gymv\models\QueryFactory;

class DashboardController extends \yii\web\Controller
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
        // build cards info
        // count all members
        $membersCount = QueryFactory::findQueryMembers()->count();

        // bank account balance info
        $bankAccount = BankAccount::findOne(SessionContact::getBankAccountId());
        $balanceInfo = $bankAccount->getBalanceInfo();

        // Number of course sold for the current  date range
        $countCourses = QueryFactory::findCourseSold()->count();

        return $this->render('index', [
            'membersCount' => $membersCount,
            'solde' => $balanceInfo['value'],
            'totalDeb' => $balanceInfo['totalDeb'],
            'totalCred' => $balanceInfo['totalCred'],
            'countCourses' => $countCourses,
            'urlMember' => Url::toRoute(['member/home']),
            'urlBankAccount' => Url::toRoute(['/bank-account/view', 'id' => SessionContact::getBankAccountId()]),
            'urlCourseOrders' => Url::toRoute(['/gymv/course']),
        ]);
    }

}
