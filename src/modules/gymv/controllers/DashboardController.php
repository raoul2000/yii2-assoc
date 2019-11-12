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
        // find all contact Id for contact who have consumed date range valid orders for products 51 or 52
        /*
        $membersContactIds = Order::find()
            ->select('to_contact_id')
            ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
            ->where(['in', 'product_id', [51,52]])
            ->distinct()
            ->asArray()
            ->all();
        */
/*
        $members = Contact::find()
            ->select('id')
            ->asArray()
            ->where(['is_natural_person' => true])
            ->with([
                'toOrders' => function ($query) {
                    $query
                        ->andWhere(['in', 'product_id', [52,53]])
                        ->validInDateRange(
                            SessionDateRange::getStart(), 
                            SessionDateRange::getEnd()
                        );
                },
            ])
            ->all();
*/
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

        // bank account balance info
        $bankAccount = BankAccount::findOne(SessionContact::getBankAccountId());
        $balanceInfo = $bankAccount->getBalanceInfo();

        // Order consmued belonging to 
        $courseProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE);
        // TODO: query below does not take into account refund. If a course has been refunded to the
        // contact, then it will still appear as owned by the contact
        $countCourses = Order::find()
            ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
            ->where(['in', 'product_id', $courseProductIds] )
            ->andWhere(['in', 'to_contact_id', $memberIds])
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
