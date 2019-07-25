<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Transactions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-index">

    <h1>
        <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <?php Pjax::begin(); ?>

    <div class="pull-right">
        <?= Html::a('<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Manage Transaction Packs', ['transaction-pack/index'], ['class' => 'btn btn-info',  'data-pjax'=>0]) ?>
    </div>    
    <p>
        <?= Html::a('Create Transaction', ['create'], ['class' => 'btn btn-success']) ?>
        <?= \app\components\widgets\DateRangeWidget::widget() ?>
    </p>

    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-hover table-condensed'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'label'     => 'N°'
            ],
            [
                'attribute' => 'type',
                'filter'    => \app\components\Constant::getTransactionTypes(),
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::encode(\app\components\Constant::getTransactionType($model->type));
                }
            ],
            [
                'attribute' => 'from_account_id',
                'filter'    => $bankAccounts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                    return Html::a(
                        Html::encode($bankAccounts[$model->from_account_id]),
                        ['bank-account/view','id'=>$model->from_account_id],
                        [ 'data-pjax' => 0 ]
                    );
                }
            ],
            [
                'attribute' => 'to_account_id',
                'filter'    =>  $bankAccounts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                    return Html::a(
                        Html::encode($bankAccounts[$model->to_account_id]),
                        ['bank-account/view','id'=>$model->to_account_id],
                        [ 'data-pjax' => 0 ]
                    );
                }
            ],
            'code',
            'value',
            'description',
            'is_verified:boolean',
            'reference_date:date',
            //'orderValuesDiff:orderValuesDiff',
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['nowrap' => 'nowrap']
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
