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

class ContactController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $countPerson = Contact::find([
            'is_natural_person' => true
        ])->count();

        $results = Yii::$app->db->createCommand(
            'SELECT count(*) as total, gender, YEAR(CURDATE()) - YEAR(birthday) AS age  
             FROM contact 
             where 
                is_natural_person is TRUE 
                and (gender = 2 or gender = 1) 
                and birthday is not null
            group by age, gender;'
        )->queryAll();
        
        $serieMan = array_fill(0, 200, 0);
        $serieWom = array_fill(0, 200, 0);
        $serie = null;
        $maxAge = 0;
        foreach ($results as $result) {
            $age = intVal($result['age']);
            $total = intval($result['total']);

            if ($result['gender'] == 1) {
                $serieMan[$age] = $total;
            } else {
                $serieWom[$age] = $total;
            }

            if ($result['age'] > $maxAge) {
                $maxAge = $age;
            }
        }
        $serieMan = array_slice($serieMan, 0, $maxAge);
        $serieWom = array_slice($serieWom, 0, $maxAge);

        return $this->render('index', [
            'countPerson' => $countPerson,
            'serieMan' => $serieMan,
            'serieWom' => $serieWom
        ]);
    }
}
