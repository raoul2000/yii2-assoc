<?php

namespace app\modules\gymv\models;

use Yii;
use yii\base\Model;
use \app\models\Product;


class ProductSelectionForm extends Model
{
    const GROUP_1 = 'group-1';
    const GROUP_2 = 'group-2';

    public $product_ids = [];

    private $_cat1_product_ids = [];

    public function rules()
    {
        return [
            [['product_ids'], 'safe'], 
            ['product_ids', 'default', 'value' => []],
        ];
    }    

    public function setCategory1ProductIds($ids)
    {
        $this->_cat1_product_ids = $ids;
    }

    public function getSelectedProductIdsByGroup($group) 
    {
        return $this->getSelectedProductIds($group);
    }
    /**
     * Returns the ids of all selectd products for a given categorey or if not
     * group is provided, returns all currently selected product ids
     *
     * @param string $group
     * @return [string] a list of product ids
     */
    public function getSelectedProductIds($group = null)
    {
        $result = [];
        if ($group === null) {
            return $this->product_ids;
        }
        foreach ($this->product_ids as  $id) {
            if (\in_array($id, $this->_cat1_product_ids)) {
                if ($group == self::GROUP_1) {
                    $result[] = $id;
                }
            } else {
                if ($group == self::GROUP_2) {
                    $result[] = $id;
                }
            }
        }
        return $result;
    }

    public function querySelectedProductModels($group = null)
    {
        return \app\models\Product::find()
            ->where(['in', 'id', $this->getSelectedProductIds($group)]);
    }  

    /**
     * Returns the list of product id that have been configured fro the given group.
     * 
     * This function is used during the registration wizard, at the product selection step.
     * Depending on group configuration, products are displayed in a different way. 
     * 
     * please check params.php for more.
     *
     * @param string $groupName
     * @return [string]
     */
    static public function getProductIdsByGroup($groupName) 
    {
        // is there a configuration for product groups ? 
        if( !array_key_exists('registration.product.group', Yii::$app->params)) {
            return [];
        }
        $groupConf = \Yii::$app->params['registration.product.group'];

        // is there a configuration for this particlar group $groupName ?
        if (!array_key_exists($groupName, $groupConf)) {
            return [];
        }

        // find what are the product that match this group configuration
        $conf = $groupConf[$groupName];

        $result = [];
        $query = Product::find()
            ->select('id')
            ->asArray();

        $productIdKey =  $categoryIdKey = false;

        if (array_key_exists('productId', $conf) && is_array($conf['productId'])) {
            $query->andWhere([ 'in', 'id', $conf['productId']]);
            $productIdKey = true;
        }
        if (array_key_exists('categoryId', $conf) && is_array($conf['categoryId'])) {
            $query->orWhere([ 'in', 'category_id', $conf['categoryId']]);
            $categoryIdKey = true;
        }

        // if neither productId nor categoryId is configured, return an empty array
        if ( $productIdKey || $categoryIdKey) {
            foreach ($query->all() as $product) {
                $result[] = $product['id'];
            }
        } 
        return $result;
    }    
}
