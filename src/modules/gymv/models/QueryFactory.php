<?php

namespace app\modules\gymv\models;

use Yii;
use app\models\Contact;
use app\models\Order;
use app\models\Product;
use yii\helpers\ArrayHelper;
use \app\components\SessionDateRange;
use \app\components\SessionContact;
use \app\components\helpers\ConverterHelper;

/**
 * ContactSearch represents the model behind the search form of `app\models\Contact`.
 */
class QueryFactory
{
    // not used
    static public function getCoursesMap()
    {
        $productIdsAsString = Yii::$app->configManager->getItemValue('product.consumed.by.registered.contact');
        $productIds = ConverterHelper::explode(',',$productIdsAsString);

        $products = Product::find()
            ->select(['id','name'])
            ->where(['in', 'id', $productIds])
            ->asArray()
            ->all();
            
        return ArrayHelper::map($products, 'id', 'name');
    }

    /**
     * Find all Contact persons considered as members and for 
     * the current date range.
     *
     * @return ActiveQuery
     */
    static public function findQueryMembers()
    {
        // read list of product ids identifying a registered contact (from config)
        $productIdsAsString = Yii::$app->configManager->getItemValue('product.consumed.by.registered.contact');
        $productIds = ConverterHelper::explode(',',$productIdsAsString);

        // search contact having valid order for those products : they are registered members
        return Contact::find()
            ->andWhere(['is_natural_person' => true])
            ->joinWith([
                'toOrders' => function($q) use($productIds) {
                    $q
                        ->from(['o' => Order::tableName()])
                        ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                        ->andWhere(['in', 'o.product_id', $productIds]);
                }
            ]);        
    }

    /**
     * Find All orders for course products that have been provided by the current contact (session)
     * and for the current date range.
     *
     * @param [ids] $courseProductIds (optional) list of product ids to consider as courses. If not provided
     * use configuration
     * @return ActiveQuery
     */
    static public function findCourseSold($courseProductIds = null)
    {
        // list of product ids for all products belonging to the group COURSE
        $courseProductIds = $courseProductIds === null 
            ? ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_COURSE)
            : $courseProductIds;
        
        // TODO: query below does not take into account refund. If a course has been refunded to the
        // contact, then it will still appear as owned by the contact

        return Order::find()
            ->where(['in', 'product_id', $courseProductIds])
            ->andWhere(['from_contact_id' => SessionContact::getContactId()])
            ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd());
    }
}