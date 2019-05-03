<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\RecordHistory;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RecordHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Record History';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="record-history-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-hover table-condensed'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'table_name',
                'filter'    => RecordHistory::getTableName(),
                'format'    => 'html',
                'value'     => function ($model, $key, $index, $column) {
                    return RecordHistory::getTableName($model->table_name);
                }
            ],
            'row_id',
            [
                'attribute' => 'event',
                'filter'    => RecordHistory::getEventName(),
                'format'    => 'html',
                'value'     => function ($model, $key, $index, $column) {
                    return RecordHistory::getEventName($model->event);
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'attribute' => 'created_by',
                'filter'    => $usernames,
                'format'    => 'html',
                'value'     => function ($model, $key, $index, $column) {
                    return ($model->user != null ? $model->user->username : null);

                }
            ],
            'field_name',
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}']
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
