<?php

namespace app\controllers\admin;

class HomeController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
