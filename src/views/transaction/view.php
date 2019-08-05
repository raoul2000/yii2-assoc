<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Transaction */

$this->title = 'N°' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$transactionModel = $model;
?>
<div class="transaction-view">

    <h1>
        <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> 
        Transaction <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Create Another Transaction', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('View Complete', ['view-complete', 'id'=> $model->id], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="row">
        <div class="col-lg-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'From',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> ' . Html::encode($model->fromAccount->longName),
                                ['bank-account/view','id' => $model->fromAccount->id],
                                ['title' => 'view Account', 'data-pjax' => 0]
                            );
                        }
                    ],
                    [
                        'label' => 'To',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> ' . Html::encode($model->toAccount->longName),
                                ['bank-account/view','id' => $model->toAccount->id],
                                ['title' => 'view Account', 'data-pjax' => 0]
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
                    'is_verified:boolean'
                ],
            ]) ?>        
        </div>
        <div class="col-lg-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Category',
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
                        'label' => 'pack',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->transaction_pack_id) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> '
                                        . Html::encode('pack N°' . $model->transaction_pack_id . ' - ' . $model->pack->name),
                                    ['transaction-pack/view', 'id' => $model->transaction_pack_id],
                                    ['title' => 'view pack', 'data-pjax' => 0]
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
                ],
            ]) ?>        
        </div>
            
    </div>


    <div class="tab-view">
        <?= yii\bootstrap\Nav::widget([
            'options' => ['class' =>'nav-tabs'],
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Orders',
                    'encode' => false,
                    'url' => ['view', 'id' => $model->id,'tab'=>'orders'],
                    'active' => $tab == 'orders'
                ],
                [
                    'label' => '<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> Attachment',
                    'encode' => false,
                    'url' => ['view', 'id' => $model->id,'tab'=>'attachment'],
                    'active' => $tab == 'attachment'
                ],
            ]
        ]) ?>

        <div style="margin-top:1em;">
            <?= $tabContent ?>
        </div>
    </div>
    
</div>
