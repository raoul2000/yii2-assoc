<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RecordHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Record History';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="record-history-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'table_name',
            'row_id',
            'event',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            'created_by',
            'field_name',
            //'old_value:ntext',
            //'new_value:ntext',
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}']
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
