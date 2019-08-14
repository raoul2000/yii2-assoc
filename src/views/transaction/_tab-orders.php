<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$transactionModel = $model;
?>
<div class="tab-orders">
    <p>
        <?= Html::a('Create Order For This Transaction', [
            'order/create',
            'transaction_id' => $model->id,
            'contact_id' => $model->fromAccount->contact->id
            ], ['class' => 'btn btn-success', 'data-pjax'=>0]) ?>
        
        <?= Html::a('Link To Existing Order', [
            'link-order',
            'id' => $model->id
            ], ['class' => 'btn btn-primary', 'data-pjax'=>0]) ?>
    </p>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-hover table-condensed'],
        'dataProvider' => $orderDataProvider,
        'filterModel'  => $orderSearchModel,
        'columns' => [
            [
                'attribute' => 'product_id',
                'label'     => 'Product',
                'filter'    => $products,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($products) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-gift" aria-hidden="true"></span> '
                         . Html::encode($products[$model->product_id]),
                        ['product/view','id'=>$model->product_id],
                        [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view product')]
                    );
                }
            ],
            [
                'attribute' => 'to_contact_id',
                'label'     => 'Beneficiary',
                'filter'    => $contacts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($contacts) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                            . Html::encode($contacts[$model->to_contact_id]),
                        ['contact/view','id'=>$model->to_contact_id],
                        [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view contact')]
                    );
                }
            ],
            'value',
            'valid_date_start:appDate',
            'valid_date_end:appDate',
            [
                'class' => 'yii\grid\ActionColumn',
                'template'  => '{view} {unlink} ',
                'contentOptions' => ['nowrap' => 'nowrap'],
                'urlCreator' => function ($action, $model, $key, $index) use ($transactionModel) {
                    if ($action == 'unlink') {
                        return Url::to(['unlink-order', 'id' =>  $transactionModel->id, 'order_id' => $model->id, 'redirect_url' => Url::current()]);
                    }
                    return Url::to(['order/' . $action, 'id' => $model->id]);
                },
                'buttons'   => [
                    'unlink' => function ($url, $order, $key) use ($transactionModel) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-remove"></span>',
                            $url,
                            ['title' => 'unlink', 'data-pjax' => 0, 'data-confirm' => 'Are you sure you want to unlink this order ?']
                        );
                    },
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
