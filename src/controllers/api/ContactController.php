<?php

namespace app\controllers\api;

use Yii;
use yii\rest\ActiveController;
use \app\models\Contact;
use \app\models\ContactSearch;
use yii\data\ActiveDataProvider;

class ContactController extends BaseAPIController
{
    public $modelClass = '\app\models\Contact';

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
            'query' => Contact::find()
                ->where(['like', 'name', $name])
                ->andWhere(['is_natural_person' => true])
                ->with('address'),
            'sort'=> [
                'defaultOrder' => [
                    'name' => SORT_DESC
                ]
            ]
        ]);
    }
}
