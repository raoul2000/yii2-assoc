<?php

namespace app\modules\gymv\models;

use yii\base\Model;

class ProductSelectionForm extends Model
{
    const CATEGORY_1 = 'category-1';
    const CATEGORY_2 = 'category-2';

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

    public function getSelectedProductIdsByCategory($category) 
    {
        return $this->getSelectedProductIds($category);
    }
    /**
     * Returns the ids of all selectd products for a given categorey or if not
     * category is provided, returns all currently selected product ids
     *
     * @param string $category
     * @return [string] a list of product ids
     */
    public function getSelectedProductIds($category = null)
    {
        $result = [];
        if ($category === null) {
            return $this->product_ids;
        }
        foreach ($this->product_ids as  $id) {
            if (\in_array($id, $this->_cat1_product_ids)) {
                if ($category == self::CATEGORY_1) {
                    $result[] = $id;
                }
            } else {
                if ($category == self::CATEGORY_2) {
                    $result[] = $id;
                }
            }
        }
        return $result;
    }

    public function querySelectedProductModels($category = null)
    {
        return \app\models\Product::find()
            ->where(['in', 'id', $this->getSelectedProductIds($category)]);
    }
}
