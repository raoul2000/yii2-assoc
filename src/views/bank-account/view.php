<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\BankAccount */

$this->title = $model->longName;
$this->params['breadcrumbs'][] = ['label' => 'Bank Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$bankAccountModel = $model;
?>
<div class="bank-account-view">

    <h1>
        <span class="glyphicon glyphicon-euro" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
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
        <?= Html::a('Create Another Bank Account', ['create', 'contact_id' => $model->contact_id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Contact',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' .  Html::encode($model->contact_name),
                        ['contact/view', 'id' => $model->contact_id, 'tab'=>'account'],
                        ['title' => 'view contact']
                    );
                }
            ],
            'name',
            [
                'label' => 'Current Value',
                'format' => 'raw',
                'value' => function ($model) use ($accountBalance) {
                    return '<b>' . Html::encode($accountBalance['value']) . '</b>'
                    . ' (<em>total debit : ' . $accountBalance['totalDeb'] . '</em>'
                    . ' / <em>total credit : ' . $accountBalance['totalCred'] . '</em>)' ;
                }
            ],
            'initial_value',
            /*
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],*/
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

    <?= yii\bootstrap\Nav::widget([
        'options' => ['class' =>'nav-tabs'],
        'items' => [
            [
                'label' => '<span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> Transaction',
                'encode' => false,
                'url' => ['view', 'id' => $model->id,'tab'=>'transaction'],
                'active' => $tab == 'transaction'
            ],
            [
                'label' => 'Pack',
                'url' => ['view', 'id' => $model->id,'tab'=>'pack'],
                'active' => $tab == 'pack'
            ],
        ]
    ]) ?>

    <div style="margin-top:1em;">
        <?= $tabContent ?>
    </div>
    
</div>
