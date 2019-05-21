<?php

use Da\User\Controller\SecurityController;
use Da\User\Event\FormEvent;
use yii\base\Event;
use app\components\SessionContact;
use app\models\Contact;
use app\models\BankAccount;
use yii\base\Application;

Event::on(Application::class, Application::EVENT_BEFORE_ACTION, function ($event) {

    if (Yii::$app->user->isGuest) {
        return;
    }

    try {
        if (SessionContact::getContactId() === null) {
            SessionContact::loadFromConfig();
        }
    } catch (Exception $e) {
        // fail to set contact info
    }
});
