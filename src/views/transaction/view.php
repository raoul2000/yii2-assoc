<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Transaction */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="transaction-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fromAccount.longName',
            'toAccount.longName',
            'value',
            'description',
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
        ],
    ]) ?>

    <h2>Orders</h2>
    <?php Pjax::begin(); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('Create Order For This Transaction', [
                'order/create',
                'transaction_id' => $model->id,
                'contact_id' => $model->fromAccount->contact->id
                ], ['class' => 'btn btn-success']) ?>
        </p>

        <?= GridView::widget([
            'dataProvider' => $orderDataProvider,
            'filterModel' => $orderSearchModel,
            'columns' => [
                [
                    'attribute' => 'product_id',
                    'filter'    => $products,
                    'format'    => 'html',
                    'value'     => function ($model, $key, $index, $column) use ($products) {
                        return Html::a(Html::encode($products[$model->product_id]), ['product/view','id'=>$model->product_id]);
                    }
                ],
                'quantity',
                'contact_id',
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
                    'template' => '{view} {delete}',
                    'urlCreator' => function ($action, $model, $key, $index) {
                        return Url::to(['order/' . $action, 'id' => $model->id]);
                    }
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>    

</div>
