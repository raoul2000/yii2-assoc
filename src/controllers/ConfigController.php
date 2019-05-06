<?php

namespace app\controllers;

use Yii;
use yii\base\Model;
use yii\web\Controller;

class ConfigController extends Controller
{
    /**
        * Performs batch updated of application configuration records.
        */
    public function actionIndex()
    {
        /* @var $configManager \yii2tech\config\Manager */
        $configManager = Yii::$app->get('configManager');

        $models = array_filter($configManager->getItems(), function ($m) {
            return ! \in_array($m->id, ['contact_id', 'bank_account_id']);
        });

        if ( count($models) != 0 && Model::loadMultiple($models, Yii::$app->request->post()) && Model::validateMultiple($models)) {
            $configManager->saveValues();
            Yii::$app->session->setFlash('success', 'Configuration updated.');
            return $this->refresh();
        }

        return $this->render('index', [
            'models' => $models,
        ]);
    }

    /**
        * Restores default values for the application configuration.
        */
    public function actionDefault()
    {
        /* @var $configManager \yii2tech\config\Manager */
        $configManager = Yii::$app->get('configManager');
        $configManager->clearValues();
        Yii::$app->session->setFlash('success', 'Default values restored.');
        return $this->redirect(['index']);
    }
}