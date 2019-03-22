<?php

namespace app\controllers\admin;

class HomeController extends \yii\web\Controller
{
    public $container_fluid = true;

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
                        'roles' => ['@'],
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
