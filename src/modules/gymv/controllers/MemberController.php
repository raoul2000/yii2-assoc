<?php

namespace app\modules\gymv\controllers;

use Yii;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\OrderSearch;
use \app\models\Order;
use \app\components\SessionDateRange;
use \app\components\helpers\ConverterHelper;
use \app\modules\gymv\models\MemberSearch;
use \app\modules\gymv\models\QueryFactory;
use app\modules\gymv\models\ProductSelectionForm;
use yii\db\Query;
use app\modules\gymv\models\ProductCourseSearch;
use yii\web\NotFoundHttpException;

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

    /**
     * View a list of all members
     *
     * @return void
     */
    public function actionIndex()
    {
  
        // read list of product ids identifying a registered contact (from config)
        $productIdsAsString = Yii::$app->configManager->getItemValue('product.consumed.by.registered.contact');
        $productIds = ConverterHelper::explode(',',$productIdsAsString);

        // search contact having valid order for those products : they are registered members
        $query = Contact::find()
            ->where(['is_natural_person' => true])
            ->joinWith([
                'toOrders' => function($q) use($productIds) {
                    $q
                        ->from(['o' => Order::tableName()])
                        ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                        ->andWhere(['in', 'o.product_id', $productIds]);
                }
            ]);   
  
/*
        $query = \app\modules\gymv\models\Member::find()
                ->joinWith('membershipOrders');
*/                
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

    $qryCourseProductIds = \app\models\Product::find()
        ->select('id')
        ->where(['in', 'category_id', Yii::$app->params['courses_category_ids']]);

    // idea : use  sub query to find all ids of member contact
    $qryMemberContactIds = Contact::find()
        ->select('c.id')
        ->where(['c.is_natural_person' => true])
        ->joinWith([
            'toOrders' => function($q) {
                $q
                    ->andWhere(['in', 'product_id', Yii::$app->params['products_membership_ids']])
                    ->andWhere(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange());
            }
        ]);

    // works fine but should be restricted to members only (not the case now)
    $query = Contact::find()
        ->from(['c' => Contact::tableName()])
        ->where([
            'is_natural_person' => true,
            'o.id' => null])
        //->andWhere(['in', 'c.id', $qryMemberContactIds])
        ->joinWith([
            'toOrders' => function($q) use($qryCourseProductIds) {
                $q
                    ->from(['o' => Order::tableName()])
                    ->andOnCondition(['in', 'o.product_id', $qryCourseProductIds])
                    ->andOnCondition(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange());
            }
        ]);


/*
        $subQuery = Order::find()
                ->from(['o' => Order::tableName()])
                ->where(['AND',
                    ['=', 'c.id', 'o.to_contact_id'],
                    ['in', 'o.product_id', $courseProductIds]
                ]);

        $query = Contact::find()
            ->from(['c' => Contact::tableName()])
            ->where(['c.is_natural_person' => true])
            ->andWhere(['not exists', $subQuery]);
*/
        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $query
        );

        return $this->render('no-course', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
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

        $courseProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE);

        $query = Order::find()
            ->where(['in', 'product_id', $courseProductIds])
            ->andWhere(['to_contact_id' => $contact->id])
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
}
