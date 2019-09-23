<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
//$this->registerJs(file_get_contents(__DIR__ . '/address.js'), View::POS_READY, 'registration-address');
?>
<div>
    <h1>Commit</h1>
    <hr/>
    <h2>Contact</h2>
    <?= DetailView::widget([
        'model' => $contact,
        'attributes' => [
            'name',
            'firstname',
            'email:email',
            'gender:gender'
        ],
    ]) ?>    

    <h2>Address</h2>
    <?= DetailView::widget([
        'model' => $address,
        'attributes' => [
            'line_1',
            'line_2',
            'line_3',
            'zip_code',
            'city',
            'country',
            'note',
        ],
    ]) ?>    

    <h2>Orders</h2>
    <?php 
        $totalOrderValue = 0;
        foreach ($orders as $order):
    ?>
        <h3><?=  Html::encode($order->product->name) ?></h3>
        <?= DetailView::widget([
            'model' => $order,
            'attributes' => [
                'value',
                'valid_date_start:appDate',
                'valid_date_end:appDate',                
            ],
        ]) ?>
        <?php $totalOrderValue += $order->value; ?>
    <?php endforeach ?>
    <div class="pull-right">Total : <?= $totalOrderValue ?></div>
    <div class="clearfix"></div>


    <h2>Transactions</h2>
    <?php $totalTransactionValue = 0; ?>
    <?php foreach ($transactions as $transaction):?>
        <h3><?=  Html::encode($order->product->name) ?></h3>
        <?= DetailView::widget([
            'model' => $transaction,
            'attributes' => [
                'value',
                [
                    'attribute' => 'type',
                    'format'    => 'raw',
                    'value'     => function ($model) {
                        return Html::encode(\app\components\Constant::getTransactionType($model->type));
                    }
                ],
                'reference_date:appDate',                
            ],
        ]) ?>
         <?php $totalTransactionValue += $transaction->value; ?>
    <?php endforeach ?>
    <div class="pull-right">Total : <?= $totalTransactionValue ?></div>
    <div class="clearfix"></div>    
</div>