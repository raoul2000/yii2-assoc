<?php

namespace app\modules\stat\controllers;

use Yii;

use yii\filters\AccessControl;
use app\models\Address;
use app\models\Contact;
use yii\helpers\Html;

class AddressController extends \yii\web\Controller
{
    const COUNT_PER_CITY_THRESHOLD = 4;

    public function actionIndex($downloadData = false)
    {
        $contactIds  = $this->findAllContactId(true);
            
        $rows = (new \yii\db\Query())
            ->select(['count(*) AS total', 'city'])
            ->from(Address::tableName())
            ->where(['in' , 'id', $contactIds ])
            ->groupBy('city')
            ->all();

        $serie = [
            'name' => 'Brands',
            'colorByPoint' => true,
            'data' => []
        ];

        $keyName = $downloadData  ? 'city' : 'name';
        $keyY = $downloadData  ? 'total' : 'y';

        foreach ($rows as $result) {
            // when requesting data download, do not apply threshold
            if (!$downloadData) {
                if ($result['total'] < self::COUNT_PER_CITY_THRESHOLD) {
                    continue;
                }
            }
            $serie['data'][] = [
                $keyName => $result['city'],
                $keyY    => intVal($result['total'])
            ];
        }

        if ($downloadData) {
            Yii::$app->response->statusCode = 200;
            //$response->format = \yii\web\Response::FORMAT_JSON;
            return serialize($serie);
            //$response->data = ['message' => 'hello world'];
        } else {
            //. '<a href="http://localhost/dev/ws/lab/yii2-assoc/src/web/index.php?r=stat%2Faddress%2Findex&downloadData=1">download</a>'

            // using . Html::a('(download)', ['index', 'downloadData' => true]), in the subTitle causes the query parameter separator '&' to be
            // converted into XMl entoty, which breaks parameter passing
            return $this->render('index', [
                'title' => 'Répartition Géographique',
                'subTitle' => 'Villes comportant plus de ' . self::COUNT_PER_CITY_THRESHOLD . ' contact (personne) enregistrées',
                'countPerson' => count($contactIds),
                'serie' => $serie,
            ]);
        }
    }

    private function findAllContactId($isPerson)
    {
        $rows = Contact::find()
            ->select('id')
            ->where([ 'is_natural_person' => $isPerson])
            ->asArray()
            ->all();

        $personContactIds = array_map(function ($item) {
            return $item['id'];
        }, $rows);

        return $personContactIds;
    }
}
