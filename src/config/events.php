<?php


use yii\base\Event;
use app\components\SessionContact;
use yii\base\Application;
use \app\components\SessionDateRange;

/**
 * Install global event handlers
 * (Loaded by bootstrap script web/index.php)
 */
/*
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
*/

Event::on( 
    \Da\User\Controller\SecurityController::class, 
    \Da\User\Event\FormEvent::EVENT_AFTER_LOGIN, 
    function($event) {

        // load current contact from config
        SessionContact::loadFromConfig();

        // load current date range from config
        SessionDateRange::loadFromConfig();

    }
); 
 
