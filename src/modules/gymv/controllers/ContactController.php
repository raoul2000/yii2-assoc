<?php

namespace app\modules\gymv\controllers;

use yii\web\Controller;

/**
 * Default controller for the `gymv` module
 */
class ContactController extends \app\controllers\ContactController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex2()
    {
        return $this->render('index');
    }
}
