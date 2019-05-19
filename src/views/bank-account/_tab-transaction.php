<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;

$bankAccountModel = $model;
?>
<div class="tab-transaction">
    <p>
        <?= Html::a('Create Debit', ['transaction/create', 'from_account_id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Create Credit', ['transaction/create', 'to_account_id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $transactionDataProvider,
            'columns' => [
                'reference_date:date',
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
                'code',
                [
                    'attribute' => 'label',
                    'format'    => 'html',
                    'value'     => function ($transactionModel, $key, $index, $column) {
                        return Html::encode($transactionModel->description);
                    }
                ],
                [
                    'attribute' => 'account',
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
                    'format'    => 'html',
                    'value'     => function ($transactionModel, $key, $index, $column) use ($bankAccountModel) {
                        return $transactionModel->from_account_id == $bankAccountModel->id
                        ? ''
                        : $transactionModel->value;
                    }
                ],
                [
                    'attribute' => 'Débit',
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