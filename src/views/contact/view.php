<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$contactModel = $model;
$allAttachments = $model->attachments;

?>
<div class="contact-view">

    <h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Create Another Contact', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('View Orders', ['order/index', 'OrderSearch[contact_id]' => $model->id], ['class' => 'btn btn-default']) ?>
        <?= Html::a('View Accounts', ['bank-account/index', 'BankAccountSearch[contact_id]' => $model->id], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'firstname',
            'gender:gender',
            'birthday:date',
            'is_natural_person:boolean',
            'email:email',
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'label' => 'History',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('(view)', \app\models\RecordHistory::getRecordHistoryIndex(\app\models\Contact::tableName(), $model->id));
                }
            ],
        ],
    ]) ?>

    <?php Pjax::begin(); ?>
        <?= yii\bootstrap\Nav::widget([
            'options' => ['class' =>'nav-tabs'],
            'items' => [
                [
                    'label' => 'Account',
                    'url' => ['view', 'id' => $model->id,'tab'=>'account'],
                    'active' => $tab == 'account'
                ],
                [
                    'label' => 'Attachment',
                    'url' => ['view', 'id' => $model->id,'tab'=>'attachment'],
                    'active' => $tab == 'attachment'
                ],
                [
                    'label' => 'Address',
                    'url' => ['view', 'id' => $model->id,'tab'=>'address'],
                    'active' => $tab == 'address'
                ],
            ]
        ]) ?>
        <div style="margin-top:1em;">
            <?= $tabContent ?>
        </div>
    <?php Pjax::end(); ?>
    
</div>
