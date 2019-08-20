<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Transaction */

$this->title = 'N°' . $model->id;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Transactions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$transactionModel = $model;
?>
<div class="transaction-view">

    <h1>
        <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Transaction') ?> <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <p>
        <?= Html::a(
            \Yii::t('app', 'Update'), 
            ['update', 'id' => $model->id], 
            ['class' => 'btn btn-primary']
        )?>
        <?= Html::a(
            \Yii::t('app', 'Delete'), 
            ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => \Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])?>
        <?= Html::a(
            \Yii::t('app', 'Create Another Transaction'), 
            ['create'], 
            ['class' => 'btn btn-success']
        )?>
        <?= Html::a(
            \Yii::t('app', 'View Complete'), 
            ['view-complete', 'id'=> $model->id],
            ['class' => 'btn btn-default']
        )?>
    </p>

    <div class="row">
        <div class="col-lg-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => \Yii::t('app', 'Sender Account'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> ' 
                                    . Html::encode($model->fromAccount->longName),
                                ['bank-account/view','id' => $model->fromAccount->id],
                                ['title' => \Yii::t('app', 'view account'), 'data-pjax' => 0]
                            );
                        }
                    ],
                    [
                        'label' => \Yii::t('app', 'Receiver Account'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> ' 
                                    . Html::encode($model->toAccount->longName),
                                ['bank-account/view','id' => $model->toAccount->id],
                                ['title' => \Yii::t('app', 'view account'), 'data-pjax' => 0]
                            );
                        }
                    ],
                    'value',
                    [
                        'attribute' => 'type',
                        'format'    => 'raw',
                        'value'     => function ($model) {
                            return Html::encode(\app\components\Constant::getTransactionType($model->type));
                        }
                    ],
                    'reference_date:appDate',
                    'code',
                    'is_verified:boolean',
                    'tagValues:tagsList',                  
                ],
            ]) ?>        
        </div>
        <div class="col-lg-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => \Yii::t('app', 'Category'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->category_id) {
                                return Html::encode($model->category->name);
                            } else {
                                return null;
                            }
                        }
                    ],
                    'description',
                    'orders_value_total',
                    'orderValuesDiff:orderValuesDiff',
                    [
                        'label' => \Yii::t('app', 'pack'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->transaction_pack_id) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> '
                                        . Html::encode('pack N°' . $model->transaction_pack_id . ' - ' . $model->pack->name),
                                    ['transaction-pack/view', 'id' => $model->transaction_pack_id],
                                    ['title' => \Yii::t('app', 'view pack'), 'data-pjax' => 0]
                                );
                            } else {
                                return null;
                            }
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => ['date', 'php:d/m/Y H:i']
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:d/m/Y H:i']
                    ],
                    [
                        'label' => \Yii::t('app', 'History'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                \Yii::t('app', 'view'), 
                                \app\models\RecordHistory::getHistoryUrl($model)
                            );
                        }
                    ],
                ],
            ]) ?>        
        </div>
    </div>

    <div class="tab-view">
        <?= yii\bootstrap\Nav::widget([
            'options' => ['class' =>'nav-tabs'],
            'items' => [
                [
                    'label'  => '<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> ' 
                        . \Yii::t('app', 'Orders'),
                    'encode' => false,
                    'url'    => ['view', 'id' => $model->id,'tab'=>'orders'],
                    'active' => $tab == 'orders'
                ],
                [
                    'label'  => '<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> ' 
                        . \Yii::t('app', 'Attachment'),
                    'encode' => false,
                    'url'    => ['view', 'id' => $model->id,'tab'=>'attachment'],
                    'active' => $tab == 'attachment'
                ],
            ]
        ]) ?>

        <div style="margin-top:1em;">
            <?= $tabContent ?>
        </div>
    </div>
    
</div>
