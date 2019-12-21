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
        $membersCount = MemberSearch::findQueryMembers()->count();

        // bank account balance info
        $bankAccount = BankAccount::findOne(SessionContact::getBankAccountId());
        $balanceInfo = $bankAccount->getBalanceInfo();

        // Order consmued belonging to 
        //$courseProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE);
        $courseProductIds = Yii::$app->params['courses_category_ids'];
        // TODO: query below does not take into account refund. If a course has been refunded to the
        // contact, then it will still appear as owned by the contact

        $countCourses = Order::find()
            ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
            ->where(['in', 'product_id', $courseProductIds] )
            ->andWhere(['from_contact_id' => SessionContact::getContactId()])
            ->distinct()
            ->count();

        return $this->render('index', [
            'membersCount' => $membersCount,
            'solde' => $balanceInfo['value'],
            'totalDeb' => $balanceInfo['totalDeb'],
            'totalCred' => $balanceInfo['totalCred'],
            'countCourses' => $countCourses,
            'urlMember' => Url::toRoute(['/gymv/member']),
            'urlBankAccount' => Url::toRoute(['/bank-account/view', 'id' => SessionContact::getBankAccountId()]),
            'urlCourseOrders' => Url::toRoute(['/gymv/course']),
        ]);
    }

}
