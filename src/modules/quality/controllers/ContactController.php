<?php

namespace app\modules\quality\controllers;

use yii\web\Controller;
use app\models\Contact;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `quality` module
 */
class ContactController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $allModels = [];
        foreach($this->getMetrics() as $id => $metric) {
            $allModels[] = [
                'id'  => $id,
                'label' => $metric['label'],
                'value' => $metric['query']->count()
            ];
        }

        return $this->render('index', [
            'provider' => new ArrayDataProvider([
                'allModels' => $allModels,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ])
        ]);
    }

    public function actionViewData($id)
    {
        $metrics = $this->getMetrics();
        if (!array_key_exists($id, $metrics)) {
            throw new NotFoundHttpException('Data set id not found');
        }
        $metric = $metrics[$id];

        return $this->render('view-data', [
            'id' => $id,
            'dataProvider' => new ActiveDataProvider([
                'query' => $metric['query'],
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]),
            'label' => $metric['label']
        ]);
    }

    private function getMetrics()
    {
        return [
           'email-null' => [
                'query' => Contact::find()->where([
                    'is_natural_person' => true,
                    'email' => null]),
                'label' => "Personnes dont <b>l'adresse Email</b> est manquante"
                ],
           'firstname-null' => [
                'query' => Contact::find()->where([
                    'is_natural_person' => true,
                    'firstname' => null]),
                'label' => "Personnes dont le <b>prénom</b> est manquant"
                ],
           'birthday-null' => [
                'query' => Contact::find()->where([
                    'is_natural_person' => true,
                    'birthday' => null]),
                'label' => "Personnes dont la <b>date de naissance</b> est manquante"
                ],
            'centenary' => [
                'query' => Contact::find()
                    ->where(['is_natural_person' => true])
                    ->andWhere([ 'is not', 'birthday' , null])
                    ->andWhere([ '>', 'YEAR(CURDATE()) - YEAR(birthday)' , 110]),
                'label' => "Personnes dont la <b>date de naissance</b> est à vérifier"
                ],    
           'gender-null' => [
                'query' => Contact::find()
                    ->where(['is_natural_person' => true])
                    ->andwhere(['in', 'gender', [null, 0]]),
                'label' => "Personnes dont le <b>genre</b> n'est pas déterminé"
                ],
           'address-null' => [
                'query' => Contact::find()->where([
                    'is_natural_person' => true,
                    'address_id' => null]),
                'label' => "Personnes qui ne sont pas réliées à une <b>adresse</b>"
            ]
        ];
    }
}
