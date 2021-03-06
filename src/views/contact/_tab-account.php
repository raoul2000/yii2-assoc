<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

?>
<p>
    <?= Html::a(
        \Yii::t('app', 'Create Account'), 
        ['bank-account/create', 'contact_id' => $model->id], 
        ['class' => 'btn btn-success']) 
    ?>
</p>
<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-hover table-condensed'],
    'dataProvider' => $bankAccountDataProvider,
    'columns' => [
        'name',
        'initial_value',
        [
            'label' => 'total Deb.',
            'value'     => function ($model, $key, $index, $column) {
                return $model->getBalanceInfo(false)['totalDeb'];
            }
        ],
        [
            'label' => 'total Cred.',
            'value'     => function ($model, $key, $index, $column) {
                return $model->getBalanceInfo(false)['totalCred'];
            }
        ],
        [
            'label' => 'Value',
            'format' => 'raw',
            'value'     => function ($model, $key, $index, $column) {
                return '<b>' . $model->getBalanceInfo(false)['value'] . '</b>';
            }
        ],
        [
            'class'     => 'yii\grid\ActionColumn',
            'template'  => '{view}',
            'urlCreator' => function ($action, $model, $key, $index) {
                if ($action == 'view') {
                    return Url::to(['bank-account/view', 'id' => $model->id ]);
                }
                return '';
            },
        ]
    ],
]); ?>
