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

    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Transaction Pack', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'tableOptions' 		=> ['class' => 'table table-hover table-condensed'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
                    if ($model->bank_account_id ) {
                        return Html::a(
                            Html::encode($bankAccounts[$model->bank_account_id]),
                            ['bank-account/view','id'=>$model->bank_account_id],
                            [ 'data-pjax' => 0 ]
                        );
                    } else {
                        return null;
                    }
                }
            ],

            'reference_date',
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
