<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
//$this->registerJs(file_get_contents(__DIR__ . '/address.js'), View::POS_READY, 'registration-address');
?>
<div id="wiz-order">
    <h3>Order</h3>

    <?= Html::a(
        '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> ' . \Yii::t('app', 'Previous'),
        ['product-select'],
        ['class' => 'btn btn-primary']
    )?>
</div>