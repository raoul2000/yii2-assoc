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
    public function actionSearch($name = null, $email = null)
    {
        $this->checkAccess('search');

        return new ActiveDataProvider([
            'query' => Contact::find()
                ->where([
                    'and',
                    [],
                    [],
                ])
                ->where(['like', 'name', $nameOrEmail])
                ->orWhere(['like', 'email', $nameOrEmail])
                ->andWhere(['is_natural_person' => true]),
            'sort'=> [
                'defaultOrder' => [
                    'name' => SORT_DESC
                ]
            ]
        ]);
    }
}
