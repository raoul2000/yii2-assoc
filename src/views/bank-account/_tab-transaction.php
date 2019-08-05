<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;

$bankAccountModel = $model;
?>
<div class="tab-transaction">
    <p>
        <?= Html::a('Create Debit', ['transaction/create', 'from_account_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Create Credit', ['transaction/create', 'to_account_id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $transactionDataProvider,
            'filterModel' => $transactionSearchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'label'     => 'N°',
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'style' => 'width:3em'
                    ],
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
                [
                    'attribute' => 'description',
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'placeholder' => 'enter description ...',
                        'autocomplete' => 'off'
                    ],
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
                [
                    'attribute' => 'account',
                    'attribute' => 'account_id',
                    'label'     => 'Account',
                    'filter'    => $bankAccounts,
                    'format'    => 'html',
                    'value'     => function ($transactionModel, $key, $index, $column) use ($bankAccountModel) {
                        if ($bankAccountModel->id == $transactionModel->from_account_id) {
                            return Html::encode($transactionModel->toAccount->contact_name);
                        } else {
                            return Html::encode($transactionModel->fromAccount->contact_name);
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
    <?php Pjax::end(); ?>
</div>