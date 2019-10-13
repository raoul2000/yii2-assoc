<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
$currentContactId = $model->id;
?>

<div>
    <p>
        <?= Html::a(
            \Yii::t('app', 'Create Order'), 
            ['order/create', 'to_contact_id' => $model->id, 'redirect_url' => Url::current()], 
            ['class' => 'btn btn-success']) 
        ?>
        <?= Html::a(
            \Yii::t('app', 'View Order Summary'), 
            ['contact/order-summary', 'id' => $model->id], 
            ['class' => 'btn btn-default']) 
        ?>
    </p>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-hover table-condensed'],
        'dataProvider' => $orderDataProvider,
        'filterModel' => $orderSearchModel,
        'columns' => [
            [
                'attribute' => 'from_contact_id',
                'filter'    => $contacts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($contacts, $currentContactId) {
                    if ($model->from_contact_id == $currentContactId) {
                        return '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                            . Html::encode($contacts[$model->from_contact_id]);
                    } else {
                        return Html::a(
                            '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                . Html::encode($contacts[$model->from_contact_id]),
                            ['contact/view','id'=>$model->from_contact_id, 'tab' => 'order'],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view contact')]
                        );
                    }
                }
            ],
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
                'filter'    => $contacts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($contacts, $currentContactId) {
                    if ($model->to_contact_id == $currentContactId) {
                        return '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                            . Html::encode($contacts[$model->to_contact_id]);
                    } else {
                        return Html::a(
                            '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                . Html::encode($contacts[$model->to_contact_id]),
                            ['contact/view','id'=>$model->to_contact_id, 'tab' => 'order'],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view contact')]
                        );
                    }
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


