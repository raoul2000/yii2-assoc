<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Attachment */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Attachments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="attachment-view">

    <h1>
        <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <p>
        <?= Html::a(
            \Yii::t('app', 'Preview'), 
            ['preview', 'id' => $model->id], 
            ['class' => 'btn btn-default', 'title' => \Yii::t('app', 'open in a new window'), 'target' => '_blank']
        )?>
        <?= Html::a(
            \Yii::t('app', 'Update'), 
            ['update', 'id' => $model->id], ['class' => 'btn btn-primary']
        )?>
        <?= Html::a(
            \Yii::t('app', 'Delete'), 
            ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => \Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'model',
            'hash',
            'itemId',
            'size',
            'type',
            'mime',
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'label' => \Yii::t('app', 'History'),
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(
                        \Yii::t('app', 'view'), 
                        \app\models\RecordHistory::getHistoryUrl($model)
                    );
                }
            ],
        ],
    ]) ?>

</div>
