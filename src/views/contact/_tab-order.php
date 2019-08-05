<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>

<div>
    <p>
        <?= Html::a('Create Order', ['order/create', 'to_contact_id' => $model->id, 'redirect_url' => Url::current()], ['class' => 'btn btn-success']) ?>
        <?= Html::a('View Order Summary', ['contact/order-summary', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </p>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-hover table-condensed'],
        'dataProvider' => $orderDataProvider,
        'filterModel' => $orderSearchModel,
        'columns' => [
            [
                'attribute' => 'product_id',
                'label'     => 'Product',
                'filter'    => $products,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($products) {
                    return Html::a(
                        Html::encode($products[$model->product_id]),
                        ['product/view','id'=>$model->product_id],
                        [ 'data-pjax' => 0 ]
                    );
                }
            ],
            [
                'attribute' => 'from_contact_id',
                'filter'    => $contacts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($contacts) {
                    return Html::a(
                        Html::encode($contacts[$model->from_contact_id]),
                        ['contact/view','id'=>$model->from_contact_id],
                        [ 'data-pjax' => 0 ]
                    );
                }
            ],
            [
                'attribute' => 'to_contact_id',
                'filter'    => $contacts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($contacts) {
                    return Html::a(
                        Html::encode($contacts[$model->to_contact_id]),
                        ['contact/view','id'=>$model->to_contact_id],
                        [ 'data-pjax' => 0 ]
                    );
                }
            ],
            'value',
            'valid_date_start:appDate',
            'valid_date_end:appDate',
            'transactionValuesDiff:transactionValuesDiff',
            [
                'class'     => 'yii\grid\ActionColumn',
                'template'  => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action == 'view') {
                        return Url::to(['order/view', 'id' => $model->id ]);
                    }
                    return '';
                },
            ]
    
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>


