<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

$transactionPackModel = $model;
?>
<p>
    <?= Html::a(\Yii::t('app', 'Select Transactions'), [
        'link-transaction',
        'id' => $model->id
        ], ['class' => 'btn btn-primary']) ?>
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
                    return Html::a(
                        '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                            . Html::encode($bankAccounts[$model->from_account_id]),
                        ['bank-account/view','id'=>$model->from_account_id],
                        ['data-pjax' => 0, 'title' => \Yii::t('app', 'view account')]
                    );
                }
            ],
            [
                'attribute' => 'to_account_id',
                'filter'    =>  $bankAccounts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                            . Html::encode($bankAccounts[$model->to_account_id]),
                        ['bank-account/view','id'=>$model->to_account_id],
                        ['data-pjax' => 0, 'title' => \Yii::t('app', 'view account')]
                    );
                }
            ],
            'code',
            [
                'attribute' => 'type',
                'filter'    => \app\components\Constant::getTransactionTypes(),
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::encode(\app\components\Constant::getTransactionType($model->type));
                    
                }
            ],
            'value',
            'description',
            'is_verified:boolean',
            'reference_date:appDate',
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['nowrap' => 'nowrap'],
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
                            ['title' => \Yii::t('app', 'unlink'), 'data-pjax'=>0, 'data-confirm' => \Yii::t('app', 'Are you sure you want to unlink this transaction ?')]
                        );
                    },
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>
