<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;

$bankAccountModel = $model;
?>
<div class="transaction-pack-index">
    <p>
        <?= Html::a(
            'Create Transaction Pack',
            ['transaction-pack/create', 'bank_account_id' => $bankAccountModel->id],
            ['class' => 'btn btn-success', 'title' => 'create pack for this account']
        )?>
    </p>

    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $transactionPackDataProvider,
            'filterModel' => $transactionPackSearchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'label'     => 'N°'
                ],
                'name',
                'reference_date',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'  => '{view}',
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'view') {
                            return Url::to([
                                'transaction-pack/view',
                                'id' => $model->id
                            ]);
                        }
                    },
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
