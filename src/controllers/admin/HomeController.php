<?php

namespace app\controllers\admin;

class HomeController extends \yii\web\Controller
{
    public $container_fluid = true;
    public function actions()
    {
        return [
            'create-user-contact' => [
                'class' => 'app\components\actions\CreateUserContactAction',
            ],
            'create-date-range' => [
                'class' => 'app\components\actions\CreateDateRangeAction',
            ],
            'delete-date-range' => [
                'class' => 'app\components\actions\DeleteDateRangeAction',
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
