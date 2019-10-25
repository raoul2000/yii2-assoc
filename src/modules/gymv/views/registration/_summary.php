<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
//$this->registerJs(file_get_contents(__DIR__ . '/address.js'), View::POS_READY, 'registration-address');
?>
<?php if (isset($data['contact'])) :?>
    <div class="contact">
        <h4>
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
            <?= \Yii::t('app', 'Contact') ?>
        </h4>
        <span class="fullname">
            <?= Html::encode($data['contact']['name'] . ' ' . $data['contact']['firstname']) ?>
        </span>

        <?php if(!empty($data['contact']['birthday'])): ?>
            <span class="birthday">
                <?= \Yii::t('app', 'birthday') ?> : <?= Html::encode($data['contact']['birthday']) ?>
            </span>
        <?php endif; ?>

        <?php if(!empty($data['contact']['email'])): ?>
            <span class="email">
                <?= \Yii::t('app', 'email') ?> : <?= Html::a($data['contact']['email'], null, ['href' => $data['contact']['email']]) ?>
            </span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (isset($data['address'])) :?>
    <div class="address" style="margin-top:2em">
        <h4>
            <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
            <?= \Yii::t('app', 'Address') ?>
        </h4>
        <span class="line_1">
            <?= Html::encode($data['address']['line_1']) ?>
        </span>
        <span class="zip_and_city" style="display:block">
            <?= Html::encode($data['address']['zip_code'] . ' ' . $data['address']['city']) ?>
        </span>

    </div>
<?php endif; ?>

<?php if (isset($data['orders'])) :?>
    <div class="address" style="margin-top:2em">
        <h4>
            <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
            <?= \Yii::t('app', 'Orders') ?>
        </h4>
        <?php $totalOrderValue = 0; ?>
        <?php foreach ($data['orders'] as $order) :?>
            <?php $totalOrderValue += $order['value']; ?>
            <div class="product-line">
                <span class="product-name"><?= Html::encode($order['product_name'])?></span>
                <span class="order-value"><?= $order['value']?></span>
            </div>
        <?php endforeach;?>
        <div class="product-line total-order-value" >
                <span class="product-name"><em>Total</em></span>
                <span class="order-value"><b><?= $totalOrderValue?></b></span>
            </div>            
    </div>
<?php endif; ?>