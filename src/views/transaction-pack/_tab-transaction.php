<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionPack */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Transaction Packs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$transactionPackModel = $model;
?>


<p>
    <?= Html::a('Select Transactions', [
        'link-transaction',
        'id' => $model->id
        ], ['class' => 'btn btn-primary']) ?>
</p>    

<?= GridView::widget([
    'tableOptions' 		=> ['class' => 'table table-hover table-condensed'],
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
                return Html::a(
                    Html::encode($bankAccounts[$model->from_account_id]),
                    ['bank-account/view','id'=>$model->from_account_id],
                    ['data-pjax' => 0]
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
                    ['data-pjax' => 0]
                );
            }
        ],
        'code',
        [
            'attribute' => 'type',
            'filter'    => \app\components\Constant::getTransactionTypes(),
            'format'    => 'raw',
            'value'     => function ($model, $key, $index, $column) {
                return Html::encode( \app\components\Constant::getTransactionType($model->type));
                
            }
        ],
        'value',
        'description',
        'is_verified:boolean',
        'reference_date:date',
        [
            'class' => 'yii\grid\ActionColumn',
            'template'  => '{view} {unlink} ',
            'urlCreator' => function ($action, $model, $key, $index) use ($transactionPackModel) {
                if ($action == 'unlink') {
                    return Url::to(['unlink-transaction', 'id' =>  $transactionPackModel->id, 'transaction_id' => $model->id, 'redirect_url' => Url::current()]);
                }
                return Url::to(['transaction/' . $action, 'id' => $model->id]);
            },
            'buttons'   => [
                'unlink' => function ($url, $order, $key) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-remove"></span>',
                        $url,
                        ['title' => 'unlink', 'data-pjax'=>0, 'data-confirm' => 'Are you sure you want to unlink this Transaction ?']
                    );
                },
            ]
        ],
    ],
]); ?>
