<?php

namespace app\modules\quality\controllers;

use Yii;
use yii\web\Controller;
use app\models\Contact;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `quality` module
 */
class BaseController extends Controller
{
    protected $pageSubHeader = '';
    protected $viewModelRoute = '';
    protected $dataColumnNames = [];

    public function isAjaxCall()
    {
        $accept = Yii::$app->request->headers->get('Accept');
        return Yii::$app->request->isGet == true && $accept == 'application/json';
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    protected function implActionIndex($metrics)
    {
        $this->setViewPath('@app/modules/quality/views/metric');
        $allModels = [];
        foreach ($metrics as $id => $metric) {
            $allModels[] = [
                'id'  => $id,
                'label' => $metric['label'],
                'value' => $metric['query']->count()
            ];
        }
        
        // prepare response
        $accept = Yii::$app->request->headers->get('Accept');

        if ($this->isAjaxCall()) {
            // Request must accept only "application/json" in order to get the JSON result as response
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 200;
            return $allModels;
        } else {
            return $this->render('index', [
                'pageSubHeader' => $this->pageSubHeader,
                'provider' => new ArrayDataProvider([
                    'allModels' => $allModels,
                    'pagination' => [
                        'pageSize' => 10,
                    ],
                ])
            ]);
        }
    }

    protected function implActionViewData($metrics, $id)
    {
        $this->setViewPath('@app/modules/quality/views/metric');
        if (!array_key_exists($id, $metrics)) {
            throw new NotFoundHttpException('Data set id not found');
        }
        $metric = $metrics[$id];

        return $this->render('view-data', [
            'id' => $id,
            'pageSubHeader' => $this->pageSubHeader,
            'viewModelRoute' => $this->viewModelRoute,
            'dataColumnNames' => $this->dataColumnNames,
            'dataProvider' => new ActiveDataProvider([
                'query' => $metric['query'],
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]),
            'label' => $metric['label']
        ]);
    }
}
