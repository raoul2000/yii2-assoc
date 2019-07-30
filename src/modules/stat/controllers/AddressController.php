<?php

namespace app\modules\stat\controllers;

use Yii;

use yii\filters\AccessControl;
use app\models\Contact;
use app\models\ContactSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\SessionDateRange;

class AddressController extends \yii\web\Controller
{
    const COUNT_PER_CITY_THRESHOLD = 4;

    public function actionIndex()
    {
        $countPerson = Contact::find([
            'is_natural_person' => true
        ])->count();

        $results = Yii::$app->db->createCommand(
            'SELECT count(*) as total, city
             FROM address 
            group by city;'
        )->queryAll();
        $serie = [
            'name' => 'Brands',
            'colorByPoint' => true,
            'data' => []
        ];

        foreach ($results as $result) {
            if ($result['total'] < self::COUNT_PER_CITY_THRESHOLD ) {
                continue;
            }
            $serie['data'][] = [
                'name' => $result['city'],
                'y' => intVal($result['total'])
            ];
        }

        return $this->render('index', [
            'countPerson' => $countPerson,
            'serie' => $serie,
        ]);
    }
}
