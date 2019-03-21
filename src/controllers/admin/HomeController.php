<?php

namespace app\controllers\admin;

class HomeController extends \yii\web\Controller
{
    public $container_fluid = true;
    public function actionIndex()
    {
        return $this->render('index');
    }

}
