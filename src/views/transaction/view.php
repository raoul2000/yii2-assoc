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
$transactionModel = $model;
?>
<div class="transaction-view">

    <h1>
        Transaction N°<?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Create Another Transaction', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'From',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(
                        Html::encode($model->fromAccount->longName),
                        ['bank-account/view','id' => $model->fromAccount->id],
                        ['title' => 'view Account', 'data-pjax' => 0]
                    );
                }
            ],
            [
                'label' => 'To',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(
                        Html::encode($model->toAccount->longName),
                        ['bank-account/view','id' => $model->toAccount->id],
                        ['title' => 'view Account', 'data-pjax' => 0]
                    );
                }
            ],
            'value',
            'reference_date:date',
            'code',
            'is_verified:boolean',
            'description',
            'orderValuesDiff:orderValuesDiff',
            [
                'label' => 'pack',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->transaction_pack_id) {
                        return Html::a(
                            Html::encode('pack N°' . $model->transaction_pack_id . ' - ' . $model->pack->name),
                            ['transaction-pack/view', 'id' => $model->transaction_pack_id],
                            ['title' => 'view pack', 'data-pjax' => 0]
                        );
                    } else {
                        return null;
                    }
                }
            ],
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
    <hr />
    <?php Pjax::begin(); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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

        <?= GridView::widget([
            'tableOptions' 		=> ['class' => 'table table-hover table-condensed'],
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

    <h2>Attachments</h2>
    <hr/>
    <p>
        <?= Html::a('Add Attachment', ['create-attachment', 'id' => $model->id, 'redirect_url' => Url::current() ], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php if (count($model->attachments) == 0): ?>
        no attachment
    <?php else: ?>
        <?= \app\components\widgets\AttachmentGridView::widget([
            'dataProvider' => new yii\data\ArrayDataProvider(['allModels' => $model->attachments]),            
        ]) ?>
    <?php endif; ?>

</div>
