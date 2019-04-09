<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\RecordHistory */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Record Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="record-history-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'table_name',
            [
                'label' => 'Record ID',
                'format' => 'raw',
                'value' => function ($model) {
                    $url = $model->getRecordViewUrl();
                    return $model->row_id . ($url == null
                        ? ''
                        : ' ' . Html::a('(view)', $url, ['title' => 'view Record']));
                }
            ],
            'event',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            'created_by',
            'field_name',
            'old_value:ntext',
            'new_value:ntext',
        ],
    ]) ?>

</div>
