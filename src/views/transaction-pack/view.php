<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionPack */

$this->title = 'N°' . $model->id . ' - ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['transaction/index']];
$this->params['breadcrumbs'][] = ['label' => 'Packs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$transactionPackModel = $model;
?>
<div class="transaction-pack-view">

    <h1>
        <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>  Pack N°<?= $model->id ?> : <?= Html::encode($model->name) ?>
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
        <?= Html::a('Create Another Transaction Pack', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Total Value',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<b>' . $model->getValueSum() . '</b>';
                }
            ],
            [
                'label' => 'Bank Account',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->bankAccount) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                                . Html::encode($model->bankAccount->longName),
                            ['bank-account/view','id' => $model->bankAccount->id],
                            ['title' => 'view Account', 'data-pjax' => 0]
                        );
                    } else {
                        return null;
                    }
                }
            ],
            'type',
            'name',
            'reference_date',
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
    
    <?= yii\bootstrap\Nav::widget([
        'options' => ['class' =>'nav-tabs'],
        'items' => [
            [
                'label' => '<span class="glyphicon glyphicon-transfer" aria-hidden="true"></span>  Transaction',
                'encode' => false,
                'url' => ['view', 'id' => $model->id, 'tab'=>'transaction'],
                'active' => $tab == 'transaction'
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
