<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = $order->product->name;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $order->id]];
$this->params['breadcrumbs'][] = 'link to transaction';
\yii\web\YiiAsset::register($this);
?>
<div>
    <p>
        <?= Html::a(
            '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back To Order',
            ['view', 'id' => $order->id]
        ) ?>
    </p>
    <div class="alert alert-info" role="alert">
        <p>
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Please select one transaction to pay this order
        </p>
    </div>    

    <p>
        <?= \app\components\widgets\DateRangeWidget::widget() ?>       
    </p>

    <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'tableOptions' 		=> ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $transactionDataProvider,
            'filterModel' => $transactionSearchModel,
            'columns' => [
                [
                    'class'     => 'yii\grid\ActionColumn',
                    'template'  => '{select}',
                    'buttons'   => [
                        'select' => function ($url, $transaction, $key) use ($order) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-ok"></span>',
                                ['link-transaction', 'id'=> $order->id, 'transaction_id' => $transaction->id],
                                ['title' => 'select this transaction', 'data-pjax'=>0]
                            );
                        },
                    ]
                ],
                'id',
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
                'value',
                'description',
                'is_verified:boolean',
                'reference_date:appDate',
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>