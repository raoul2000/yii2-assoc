<?php

use Da\User\Controller\SecurityController;
use Da\User\Event\FormEvent;
use yii\base\Event;
use app\components\SessionVars;
use app\models\Contact;
use app\models\BankAccount;

/**
 * AFTER_LOGIN
 * Read contact info (id/name and bank account ID/Name) from the application config and store
 * theses values into the current session.
 */
Event::on(SecurityController::class, FormEvent::EVENT_AFTER_LOGIN, function (FormEvent $event) {
    
    try {
        $contact_id = \Yii::$app->configManager->getItemValue('contact_id');
        $contact = Contact::findOne($contact_id);
        SessionVars::setContact($contact->id, $contact->name);

        $bank_account_id = \Yii::$app->configManager->getItemValue('bank_account_id');
        $bankAccount = BankAccount::findOne($bank_account_id);
        SessionVars::setBankAccount($bankAccount->id, $bankAccount->name
    );
    } catch (Exception $e) {
        // fail to set contact info
    }

});
