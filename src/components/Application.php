<?php

namespace app\components;

use Yii;
use app\models\Transaction;
use app\models\TransactionPack;
use app\models\BankAccount;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;

class Application extends \yii\web\Application
{
    public function createController($route)
    {
        /**
         * It is not possible to disable this based on current user because at this point the
         * user component is not loaded.
         * One option would be to rely to a file exist to enable/disable the overload behavior
         */
        $controller = parent::createController('gymv/' . $route);
        return $controller === false
            ? parent::createController($route)
            : $controller;
    }
}