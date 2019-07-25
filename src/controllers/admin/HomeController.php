<?php

namespace app\controllers\admin;

class HomeController extends \yii\web\Controller
{
    public $container_fluid = true;
    public function actions()
    {
        return [
            'user-contact' => [
                'class' => 'app\components\actions\UserContactAction',
            ],
            'date-range' => [
                'class' => 'app\components\actions\DateRangeAction',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' =>  \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ]
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
