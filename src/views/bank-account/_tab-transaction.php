<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;

$bankAccountModel = $model;
?>
<div class="tab-transaction">
    <p>
        <?= Html::a(
            \Yii::t('app', 'Create Debit'), 
            ['transaction/create', 'from_account_id' => $model->id], 
            ['class' => 'btn btn-success']) 
        ?>
        <?= Html::a(
            \Yii::t('app', 'Create Credit'), 
            ['transaction/create', 'to_account_id' => $model->id], 
            ['class' => 'btn btn-success']) 
        ?>
        <?= \app\components\widgets\DownloadDataGrid::widget() ?>            
    </p>
    <?php 
        Pjax::begin(); 
    ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $transactionDataProvider,
            'filterModel'  => $transactionSearchModel,
            'columns'      => [
                [
                    'attribute' => 'id',
                    'label'     => 'N°',
                ],
                'reference_date:appDate',
                [
                    'attribute' => 'pack',
                    'format'    => 'raw',
                    'value'     => function ($transactionModel, $key, $index, $column) {
                        if ($transactionModel->transaction_pack_id) {
                            return Html::a(
                                Html::encode('n°' . $transactionModel->transaction_pack_id),
                                ['transaction-pack/view','id' => $transactionModel->transaction_pack_id],
                                ['title' => 'view pack', 'data-pjax' => 0]
                            );
                        } else {
                            return ' ';
                        }
                    }
                ],
                'description',
                'code',
                [
                    'attribute' => 'type',
                    'filter'    => \app\components\Constant::getTransactionTypes(),
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) {
                        return Html::encode(\app\components\Constant::getTransactionType($model->type));
                    }
                ],
                [
                    'attribute' => 'account',
                    'attribute' => 'account_id',
                    'label'     => 'Account',
                    'filter'    => $bankAccounts,
                    'format'    => 'html',
                    'contentOptions'   => [
                        'nowrap' => true
                    ],
                    'value'     => function ($transactionModel, $key, $index, $column) use ($bankAccountModel) {
                        // in the same column render source or target account
                        if ($bankAccountModel->id == $transactionModel->from_account_id) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                                    . Html::encode($transactionModel->toAccount->contact_name),
                                ['contact/view','id'=>$transactionModel->toAccount->id],
                                ['data-pjax' => 0, 'title' => \Yii::t('app', 'view account')]
                            );
                        } else {
                            return Html::a(
                                '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                                    . Html::encode($transactionModel->fromAccount->contact_name),
                                ['bank-account/view','id'=>$transactionModel->fromAccount->id],
                                ['data-pjax' => 0, 'title' => \Yii::t('app', 'view account')]
                            );
                        }
                    }
                ],
                [
                    'attribute' => 'Crédit',
                    'attribute' => 'credit',
                    'format'    => 'html',
                    'value'     => function ($transactionModel, $key, $index, $column) use ($bankAccountModel) {
                        return $transactionModel->from_account_id == $bankAccountModel->id
                        ? ''
                        : $transactionModel->value;
                    }
                ],
                [
                    'attribute' => 'Débit',
                    'attribute' => 'debit',
                    'format'    => 'html',
                    'value'     => function ($transactionModel, $key, $index, $column) use ($bankAccountModel) {
                        return $transactionModel->from_account_id == $bankAccountModel->id
                            ? $transactionModel->value
                            : '';
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'  => '{view}',
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'view') {
                            return Url::to(['transaction/view', 'id' =>  $model->id, 'redirect_url' => Url::current()]);
                        }
                    },
                ],
            ],
        ]); ?>
    <?php 
        Pjax::end(); 
    ?>
</div>