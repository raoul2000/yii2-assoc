<?php

namespace app\modules\gymv\models;

use yii\base\Model;

class ProductForm extends Model
{
    public $top_products = [];
    public $products_2 = [];

    public function rules()
    {
        return [
            [['top_products', 'products_2'], 'safe'], 
        ];
    }

    static public function getTopProductsList()
    {
        return [
            '1' => "Adhésion GymV",
            '11' => "License Fédération",
            '12' => "Assurance optionnelle",
            '13' => "Inscription Espace Sorano",
        ];
    }
    
}
