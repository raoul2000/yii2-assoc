<?php

namespace app\controllers\api;

use Yii;
use yii\rest\ActiveController;
use \app\models\Product;
use \app\models\ProductSearch;
use yii\data\ActiveDataProvider;

class ProductController extends BaseAPIController
{
    public $modelClass = '\app\models\Product';

    public function actions()
    {
        $actions = parent::actions();
    
        // disable some actions
        unset($actions['delete'], $actions['create'], $actions['update']);

        return $actions;
    }    

    /**
     * Search a person contact given its name or/and its email
     *
     * @param string $name
     * @param string $email
     * @return void
     */
    public function actionSearch($name)
    {
        $this->checkAccess('search');

        return new ActiveDataProvider([
            'query' => Product::find()
                ->where(['like', 'name', $name]),
            'sort'=> [
                'defaultOrder' => [
                    'name' => SORT_DESC
                ]
            ]
        ]);
    }
}
