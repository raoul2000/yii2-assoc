<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Category;

/* @var $this yii\web\View */
/* @var $model app\models\Category */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="category-view">

    <h1>
        <span class="glyphicon glyphicon-th" aria-hidden="true"></span>
        <?= \Yii::t('app', 'Category') ?> : <?= Html::encode($this->title) ?>
    </h1>
    <hr/>

    <p>
        <?= Html::a(
            \Yii::t('app', 'Update'), 
            ['update', 'id' => $model->id], 
            ['class' => 'btn btn-primary']
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
        <?= Html::a(
            \Yii::t('app', 'Create Category'), 
            ['create'], 
            ['class' => 'btn btn-success']
        )?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'contact_id',
            [
                'label' => 'Type',
                'format' => 'raw',
                'value' => function ($model) {
                    return Category::getTypeName($model->type) . " ($model->type)" ;
                }
            ],
            'name',
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
