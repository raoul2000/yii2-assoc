<?php

namespace app\controllers\reports;

class BankBookController extends \yii\web\Controller
{
    /**
     * Select a bank account to produce the report view from
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionView($account_id)
    {
        return $this->render('view');
    }

}
