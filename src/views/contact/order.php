<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Orders';
\yii\web\YiiAsset::register($this);

?>

    <h2>Orders <small>for <?= Html::a(Html::encode($model->name), ['view', 'id' => $model->id], ['title' => 'view contact']) ?></small></h2>
    <hr/>
    <p>
        <?= Html::a('Create Order', ['order/create', 'to_contact_id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>
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
            'value',
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


