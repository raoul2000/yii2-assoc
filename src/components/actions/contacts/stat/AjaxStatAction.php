<?php
namespace app\components\actions\contacts\stat;

use Yii;
use yii\base\Action;
use app\models\Contact;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AjaxStatAction extends Action
{
    public function run()
    {
        /*
        if (!Yii::$app->request->isAjax) {
            throw new yii\web\ForbiddenHttpException();
        }
        */
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->buildDataset();
    }

    private function buildDataset()
    {
        $countPerson = Contact::find([
            'is_natural_person' => true
        ])->count();

        $results = Yii::$app->db->createCommand(
            'SELECT count(id) as total, gender, YEAR(CURDATE()) - YEAR(birthday) AS age  
             FROM contact 
             where 
                is_natural_person is TRUE 
                and (gender = 2 or gender = 1) 
                and birthday is not null 
            group by age 
            order by age;')
            ->queryAll();
        $serieMan = array_fill(0, 200, 0);
        $serieWom = array_fill(0, 200, 0);
        $serie = null;
        $maxAge = 0;
        foreach($results as $result) {
            $age = intVal($result['age']);
            $total = intval($result['total']);

            if ($result['gender'] == 1) {
                $serieMan[$age] = $total;    
            } else {
                $serieWom[$age] = $total;    
            }
            
            if( $result['age'] > $maxAge) {
                $maxAge = $age;
            }
        }
        $serieMan = array_slice($serieMan,0, $maxAge);
        $serieWom = array_slice($serieWom,0, $maxAge);

        return [
            'serieMan' => $serieMan,
            'serieWom' => $serieWom
        ];    
    }
}
