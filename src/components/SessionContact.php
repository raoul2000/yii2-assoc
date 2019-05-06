<?php

namespace app\components;

use Yii;
use app\components\Constant;
use app\models\Contact;
use app\models\BankAccount;

class SessionContact
{
    public static function clear()
    {
        Yii::$app->session->remove(Constant::SESS_CONTACT);
        Yii::$app->session->remove(Constant::SESS_BANK_ACCOUNT);
    }

    public static function loadFromConfig()
    {
        $conf = Yii::$app->configManager;
        $contact_id = $conf->getItemValue('contact_id');
        if ($contact_id !== null) {
            $bank_account_id = $conf->getItemValue('bank_account_id');
            SessionContact::setContact($contact_id, $bank_account_id);
            return true;
        }
        return false;
    }

    public static function setContact($id, $bank_account_id = null)
    {
        if (($contact = Contact::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested contact does not exist.');
        }

        $bankAccount = null;
        if ($bank_account_id === null) {
            $bankAccounts = $contact->bankAccounts;
            if (count($bankAccounts) != 0) {
                $bankAccount = $bankAccounts[0];
            } else {
                throw new NotFoundHttpException('The requested contact has no bank account.');
            }
        } elseif (($bankAccount = BankAccount::findOne($bank_account_id)) === null) {
            throw new NotFoundHttpException('The requested bank account does not exist.');
        }

        $session = Yii::$app->session;
        $session[Constant::SESS_CONTACT] = [
            'id' => $contact->id,
            'name' => $contact->name
        ];
        $session[Constant::SESS_BANK_ACCOUNT] = [
            'id' => $bankAccount->id,
            'name' => $bankAccount->name
        ];
    }
    
    public static function getContactId()
    {
        $session = Yii::$app->session;
        if ($session->has(Constant::SESS_CONTACT)) {
            return $session[Constant::SESS_CONTACT]['id'];
        }
        return null;
    }

    public static function getContactName()
    {
        $session = Yii::$app->session;
        if ($session->has(Constant::SESS_CONTACT)) {
            return $session[Constant::SESS_CONTACT]['name'];
        }
        return null;
    }

    public static function getBankAccountId()
    {
        $session = Yii::$app->session;
        if ($session->has(Constant::SESS_BANK_ACCOUNT)) {
            return $session[Constant::SESS_BANK_ACCOUNT]['id'];
        }
        return null;
    }

    public static function getBankAccountName()
    {
        $session = Yii::$app->session;
        if ($session->has(Constant::SESS_BANK_ACCOUNT)) {
            return $session[Constant::SESS_CONTACT]['name'];
        }
        return null;
    }
}
