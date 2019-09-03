<?php

namespace app\modules\gymv\models;

use yii\base\Model;

class ProductForm extends Model
{
    public $products_1 = [];
    public $products_2 = [];

    public function rules()
    {
        return [
            [['products_1', 'products_2'], 'safe'], 
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
