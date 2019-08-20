<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = $model->product->name;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$orderModel = $model;

?>
<div class="order-view">

    <h1>
        <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
        <?= Html::encode($model->product->name) ?>
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
        <?= Html::a('Create Another Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <div class="row">
        <div class="col-xs-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Product',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-gift" aria-hidden="true"></span> '
                                    . Html::encode($model->product->name),
                                ['product/view', 'id' => $model->product->id],
                                ['title' => 'view Product']
                            );
                        }
                    ],
                    [
                        'label' => 'Provider',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' . Html::encode($model->fromContact->name),
                                ['contact/view','id' => $model->fromContact->id],
                                ['title' => 'view Contact']
                            );
                        }
                    ],
                    [
                        'label' => 'Beneficiary',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' . Html::encode($model->toContact->longName),
                                ['contact/view','id' => $model->toContact->id],
                                ['title' => 'view Contact']
                            );
                        }
                    ],
                    'value',
                ],
            ]) ?>
        </div>

        <div class="col-xs-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'valid_date_start:appDate',
                    'valid_date_end:appDate',
                    'transactionValuesDiff:transactionValuesDiff',
                    'transactions_value_total',
                    [
                        'label' => \Yii::t('app', 'History'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                \Yii::t('app', 'view'), 
                                \app\models\RecordHistory::getHistoryUrl($model)
                            );
                        }
                    ],
        /*            
                    [
                        'attribute' => 'updated_at',
                        'format' => ['date', 'php:d/m/Y H:i']
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:d/m/Y H:i']
                    ],
        */
                ],
            ]) ?>
        </div>
    </div>

    <div class="tab-view">                
        <?= yii\bootstrap\Nav::widget([
                'options' => ['class' =>'nav-tabs'],
                'items' => [
                    [
                        'label' => '<span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> Transactions',
                        'encode' => false,
                        'url' => '',
                        'active' => true
                    ]
                ]
        ])?>


        <div style="margin-top:1em;">
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
            <?php Pjax::begin(); ?>
                <?= GridView::widget([
                    'tableOptions' => ['class' => 'table table-hover table-condensed'],
                    'dataProvider' => $transactionDataProvider,
                    'filterModel' => $transactionSearchModel,
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'label'     => 'N°'
                        ],
                        [
                            'attribute' => 'from_account_id',
                            'filter'    => $bankAccounts,
                            'format'    => 'raw',
                            'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                                return Html::a('<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                                        . Html::encode($bankAccounts[$model->from_account_id]),
                                    ['bank-account/view','id'=>$model->from_account_id],
                                    [ 'any' => '1', 'data-pjax' => '0', 'title' => \Yii::t('app', 'view account') ]
                                );
                            }
                        ],
                        [
                            'attribute' => 'to_account_id',
                            'filter'    =>  $bankAccounts,
                            'format'    => 'raw',
                            'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                                return Html::a('<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                                        . Html::encode($bankAccounts[$model->to_account_id]),
                                    ['bank-account/view','id'=>$model->to_account_id],
                                    [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view account') ]
                                );
                            }
                        ],
                        'value',
                        'reference_date:appDate',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template'  => '{view} {unlink} ',
                            'contentOptions' => ['nowrap' => 'nowrap'],
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
                                        ['title' => 'unlink', 'data-pjax'=>0, 'data-confirm' => \Yii::t('app', 'Are you sure you want to unlink this transaction ?')]
                                    );
                                },
                            ]
                        ],
                    ],
                ]); ?>
            <?php Pjax::end(); ?>    
        </div>
    </div>
</div>
