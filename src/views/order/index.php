<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1>
        <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>

    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-hover table-condensed'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
                'attribute' => 'to_contact_id',
                'label'     => 'Beneficiary',
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
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['nowrap' => 'nowrap']
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
