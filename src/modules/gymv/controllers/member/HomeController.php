<?php

namespace app\modules\gymv\controllers\member;

use Yii;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\OrderSearch;
use \app\models\Order;
use \app\models\Product;
use \app\components\SessionDateRange;
use \app\components\helpers\ConverterHelper;
use \app\modules\gymv\models\MemberSearch;
use \app\modules\gymv\models\QueryFactory;
use app\modules\gymv\models\ProductSelectionForm;
use yii\db\Query;
use app\modules\gymv\models\ProductCourseSearch;
use yii\web\NotFoundHttpException;

class HomeController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
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

    /**
     * Returns a query selecting id of all contact considered as members.
     * A contact is a member if : 
     * - it is a natural person
     * - it as orders for at least one membership product 
     * - this order is valid for the current date range
     *
     * @return void
     */
    protected function getQueryMembersId()
    {
        return Contact::find()
            ->select('c.id')
            ->from(['c' => Contact::tableName()])
            ->where(['c.is_natural_person' => true])
            ->innerJoinWith([
                'toOrders' => function($query) {
                    $query
                        ->andOnCondition(['in', 'product_id', Yii::$app->params['products_membership_ids']])
                        ->andOnCondition(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange());
                }
            ]);        
    }

    /**
     * Returns a query selecting Ids for all product considered as courses.
     * A product is considered as a course if it belong to one of the configured
     * categories.
     *
     * @return void
     */
    protected function getQueryCourseIds()
    {
        return Product::find()
            ->select('id')
            ->from(['p' => Product::tableName()])
            ->where(['in', 'p.category_id', Yii::$app->params['courses_category_ids']]);
    }
    /**
     * View a list of all members and all members with no course
     *
     * @return void
     */
    public function actionIndex($tab = 'all')
    {
        $infoTxt = '';
        switch ($tab) {
            case 'all':         // ----- all members
                $query = Contact::find()
                    ->where(['in', 'id', $this->getQueryMembersId()]);
                $infoTxt = 'liste des personnes ayant achetés une adhésion pour la période courante';
                break;
            case 'no-course':   // ------ all members not registered to course
                $query = Contact::find()
                    ->from(['c' => Contact::tableName()])
                    ->where(['o.id' => null])
                    ->andWhere(['in', 'c.id', $this->getQueryMembersId()])
                    ->joinWith([
                        'toOrders' => function($q) {
                            $q
                                ->from(['o' => Order::tableName()])
                                ->andOnCondition(['in', 'o.product_id', $this->getQueryCourseIds()])
                                ->andOnCondition(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange());
                        }
                    ]);
                    $infoTxt = 'liste pour la période courante, des adhérents n\'ayant pas acheté de cours ';
                break;
            case 'not-member':  // ------- all person not members
                $query = Contact::find()
                    ->from(['c' => Contact::tableName()])
                    ->where(['c.is_natural_person' => true])
                    ->andWhere(['not in', 'c.id', $this->getQueryMembersId()]);
                $infoTxt = 'liste des contacts enregistrés n\'ayant pas achetés d\'adhésion pour la période courante';
                break;
            default:
                throw new NotFoundHttpException('The requested page does not exist.');
                break;
        }

        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $query
        );

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'tab' => $tab,
            'infoTxt' => $infoTxt
        ]);    
    }

    /**
     * View a member
     *
     * @param integer $id contact id
     * @return void
     */
    public function actionView($id)
    {
        $contact = Contact::find()
            ->where([
                'id' => $id,
                'is_natural_person' => true
            ])
            ->one();
        if($contact === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }    

        // find all courses this member is registred to

        $query = Order::find()
            ->where(['in', 'product_id', $this->getQueryCourseIds()])
            ->andWhere(['to_contact_id' => $contact->id])
            ->andWhere(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange())
            ->with('product');

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $query
        );

        // render 

        return $this->render('view', [
            'contact' => $contact,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
        ]);    
    }



    public function actionCoursePerMember()
    {
        $courseProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE);

        $query = Contact::find()
            ->select([
                '{{contact}}.id',
                '{{contact}}.name',
                'COUNT(o.id) as order_count'
            ])        
            ->where(['is_natural_person' => true])
            ->joinWith([
                'toOrders' => function($q) use($courseProductIds) {
                    $q
                        ->from(['o' => Order::tableName()])
                        ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                        ->andWhere(['in', 'o.product_id', $courseProductIds]);
                }
            ])
            ->groupBy('{{contact}}.id')
            ->orderBy('order_count')
            ->asArray();
        
        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $query
        );

        return $this->render('course-per-member', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
        ]);    
    }

    public function actionNoCourse()
    {
        //$courseProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE);
/*
        $query = Contact::find()
            ->where([
                'is_natural_person' => true,
                'o.id' => null
            ])
            ->joinWith([
                'toOrders' => function($q) use($courseProductIds) {
                    $q
                        ->from(['o' => Order::tableName()])
                        ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                        ->andWhere(['in', 'o.product_id', $courseProductIds]);
                }
            ]);
*/

/*
select c.id 
	from contact c
		left join `order` o on c.id = o.to_contact_id 
			and o.product_id in (
				select id from product 
                where category_id in (1,2,3)
            )
    where c.is_natural_person is TRUE and o.id is null
    
    products_membership_ids
*/

        // -----------------------------------------
        // idea : use  sub query to find all ids of member contact
        $qryMemberContactIds = Contact::find()
            ->select('c.id')
            ->from(['c' => Contact::tableName()])
            ->where(['c.is_natural_person' => true])
            ->innerJoinWith([
                'toOrders' => function($q) {
                    $q
                        ->andOnCondition(['in', 'product_id', Yii::$app->params['products_membership_ids']])
                        ->andOnCondition(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange());
                }
            ]);
        $countMemberContact = $qryMemberContactIds->count();

        // -----------------------------------------
        $countAllPersonContact = Contact::find()
            ->where(['is_natural_person' => true])
            ->count();

        $countAllContact = Contact::find()->count();

        // -----------------------------------------
        $qryPersonContactNotMember = Contact::find()
            ->where([
                'is_natural_person' => true,
                'product_id'        => null
            ])
            ->joinWith([
                'toOrders' => function($q) {
                    $q
                        ->andOnCondition(['in', 'product_id', Yii::$app->params['products_membership_ids']])
                        ->andOnCondition(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange());
                }
            ]);
        $countPersonContactNoMember = $qryPersonContactNotMember->count();

        // -----------------------------------------
        $qryCourseProductIds = \app\models\Product::find()
            ->select('id')
            ->where(['in', 'category_id', Yii::$app->params['courses_category_ids']]);

        $countCourseProducts = $qryCourseProductIds->count();

        // -----------------------------------------
        // works fine but should be restricted to members only (not the case now)
        $query = Contact::find()
            ->from(['c' => Contact::tableName()])
            ->where(['o.id' => null])
            ->andWhere(['in', 'c.id', $qryMemberContactIds])
            ->joinWith([
                'toOrders' => function($q) use($qryCourseProductIds) {
                    $q
                        ->from(['o' => Order::tableName()])
                        ->andOnCondition(['in', 'o.product_id', $qryCourseProductIds])
                        ->andOnCondition(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange());
                }
            ])
            ->orderBy('c.name');
        $countMemberNoCourse = $query->count(); 

        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $query
        );

        return $this->render('no-course', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'countCourseProducts' => $countCourseProducts,
            'countMemberContact' => $countMemberContact,
            'countAllPersonContact' => $countAllPersonContact,
            'countAllContact' => $countAllContact,
            'countPersonContactNoMember' => $countPersonContactNoMember,
            'countMemberNoCourse' => $countMemberNoCourse
        ]);           
    }
}
