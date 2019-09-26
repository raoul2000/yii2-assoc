<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
//$this->registerJs(file_get_contents(__DIR__ . '/address.js'), View::POS_READY, 'registration-address');
$this->registerCss(file_get_contents(__DIR__ . '/style.css'));
?>
<div id="registration-wizard">
    <div class="row">
        <div id="wiz-main" class="col-sm-9">
            <?= $wizMain ?>
        </div>
        <div id="wiz-summary" class="col-sm-3">
            <?= $wizSummary ?>
        </div>
    </div>
</div>