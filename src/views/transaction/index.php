<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app', 'Transactions');
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
            <?= Html::a(
                '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> ' 
                    . \Yii::t('app', 'Manage Transaction Packs'), 
                ['transaction-pack/index'], 
                ['class' => 'btn btn-info',  'data-pjax'=>0]) 
            ?>
        </div>    
        <p>
            <?= Html::a(
                \Yii::t('app', 'Create Transaction'), 
                ['create'],
                ['class' => 'btn btn-success']
            )?>
            <?= \app\components\widgets\DownloadDataGrid::widget() ?>            
        </p>

        <?php  echo $this->render('_search', ['model' => $searchModel, 'tagValues' => $tagValues]); ?>    

        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                [
                    'attribute' => 'id',
                    'label'     => 'N°',
                ],
                [
                    'attribute' => 'description',
                    'filter'    => false,
                    'label'     => '',
                    'format'    => 'note'
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
                            '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                                . Html::encode($bankAccounts[$model->from_account_id]),
                            ['bank-account/view','id'=>$model->from_account_id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view account')]
                        );
                    }
                ],
                [
                    'attribute' => 'to_account_id',
                    'filter'    =>  $bankAccounts,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                                . Html::encode($bankAccounts[$model->to_account_id]),
                            ['bank-account/view','id'=>$model->to_account_id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view account')]
                        );
                    }
                ],
                'code',
                [
                    'attribute' => 'value',
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                        return '<b>' . $model->value . '</b>';
                    }
                ],
                //'value',
                //'description',
                //'is_verified:boolean',
                [
                    'attribute' => 'is_verified',
                    'format'    => 'raw',
                    'filter'    =>  [
                        '1' => \Yii::t('app', 'oui'),
                        '0' => \Yii::t('app', 'non')
                    ],
                    'value'     => function ($model, $key, $index, $column) {
                        return $model->is_verified ? \Yii::t('app', 'oui') : \Yii::t('app', 'non');
                    }
                ],
                'reference_date:appDate',
                //'orderValuesDiff:orderValuesDiff',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['nowrap' => 'nowrap']
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
