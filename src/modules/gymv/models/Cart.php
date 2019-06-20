<?php

namespace app\modules\gymv\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class Cart extends Model
{
    const PRODUCT_IDS_KEY = 'cart_product_ids';

    private $_product_ids = [];

    public function init()
    {
        $session = Yii::$app->session;
        if ($session->has(Cart::PRODUCT_IDS_KEY)) {
            $this->_product_ids = $session[Cart::PRODUCT_IDS_KEY];
        }
    }

    public function addProductIds($ids)
    {
        $this->_product_ids = array_unique(array_merge($this->_product_ids, $ids));
    }

    public function removeProductIds($ids)
    {
        $this->_product_ids = array_diff($this->_product_ids, $ids);
    }

    public function getProductIds()
    {
        return $this->_product_ids;
    }
    
    public function save()
    {
        if (count($this->_product_ids) === 0) {
            Yii::$app->session->remove(Cart::PRODUCT_IDS_KEY);
        } else {
            Yii::$app->session[Cart::PRODUCT_IDS_KEY] = $this->_product_ids;
        }
    }
}
