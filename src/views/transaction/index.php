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

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a('Create Transaction', ['create'], ['class' => 'btn btn-success']) ?>
        <?= \app\components\widgets\DateRangeWidget::widget() ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'from_account_id',
                'filter'    => $bankAccounts,
                'format'    => 'html',
                'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                    return Html::a(Html::encode($bankAccounts[$model->from_account_id]), ['bank-account/view','id'=>$model->from_account_id]);
                }
            ],
            [
                'attribute' => 'to_account_id',
                'filter'    =>  $bankAccounts,
                'format'    => 'html',
                'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                    return Html::a(Html::encode($bankAccounts[$model->to_account_id]), ['bank-account/view','id'=>$model->to_account_id]);
                }
            ],
            'code',
            'value',
            'description',
            'is_verified:boolean',
            'reference_date:date',
            'orderValuesDiff',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
