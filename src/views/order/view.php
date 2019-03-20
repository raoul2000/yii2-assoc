<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$orderModel = $model;

?>
<div class="order-view">

    <h1><?= Html::encode($model->product->name) ?></h1>

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
            'product.name',
            [
                'attribute' => 'contact.name',
                'label'     => 'Beneficiary',
            ],
            'value',
            'transactionValuesDiff',
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

    <h2>Transactions</h2>
    <hr />

    <?php Pjax::begin(); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('Create Transaction For This Order', [
                'transaction/create',
                'order_id' => $model->id,
                ], ['class' => 'btn btn-success', 'data-pjax'=>0]) ?>
            
            <?= Html::a('Link To Existing Transaction', [
                'link-transaction',
                'id' => $model->id
                ], ['class' => 'btn btn-primary', 'data-pjax'=>0]) ?>
        </p>

        <?= GridView::widget([
            'dataProvider' => $transactionDataProvider,
            'filterModel' => $transactionSearchModel,
            'columns' => [
                'id',
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
                'value',
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
                    'template'  => '{view} {unlink} ',
                    'urlCreator' => function ($action, $model, $key, $index) use ($orderModel) {
                        if ($action == 'unlink') {
                            return Url::to(['unlink-transaction', 'id' =>  $orderModel->id, 'transaction_id' => $model->id, 'redirect_url' => Url::current()]);
                        }
                        return Url::to(['transaction/' . $action, 'id' => $model->id]);
                    },
                    'buttons'   => [
                        'unlink' => function ($url, $order, $key) use ($orderModel) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-remove"></span>',
                                $url,
                                ['title' => 'unlink', 'data-pjax'=>0, 'data-confirm' => 'Are you sure you want to unlink this transaction ?']
                            );
                        },
                    ]
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>    
</div>
