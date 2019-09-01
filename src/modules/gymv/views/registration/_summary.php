<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
//$this->registerJs(file_get_contents(__DIR__ . '/address.js'), View::POS_READY, 'registration-address');
?>
<div id="wiz-summary">

    <?php if (isset($data['contact'])) :?>
        <div class="contact">
            <h4>
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
                Contact
            </h4>
            <span class="fullname">
                <?= Html::encode($data['contact']['name'] . ' ' . $data['contact']['firstname']) ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if (isset($data['address'])) :?>
        <div class="address" style="margin-top:2em">
            <h4>
                <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
                Address
            </h4>
            <span class="line_1">
                <?= Html::encode($data['address']['line_1']) ?>
            </span>
            <span class="zip_and_city" style="display:block">
                <?= Html::encode($data['address']['zip_code'] . ' ' . $data['address']['city']) ?>
            </span>

        </div>
    <?php endif; ?>
</div>