<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TransactionPackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Packs';
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['transaction/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-pack-index">

    <h1>
        <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
        <small>Transactions</small>
    </h1>    

    <hr/>    

    <p>
        <?= Html::a('Create Transaction Pack', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'label'     => 'N°'
                ],
                'name',
                [
                    'attribute' => 'bank_account_id',
                    'filter'    => $bankAccounts,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                        if ($model->bank_account_id) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                                    . Html::encode($bankAccounts[$model->bank_account_id]),
                                ['bank-account/view','id'=>$model->bank_account_id],
                                [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view account')]
                            );
                        } else {
                            return null;
                        }
                    }
                ],

                'reference_date:appDate',
                [
                    'attribute' => 'updated_at',
                    'format' => ['date', 'php:d/m/Y H:i']
                ],
                [
                    'attribute' => 'created_at',
                    'format' => ['date', 'php:d/m/Y H:i']
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['nowrap' => 'nowrap']
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
