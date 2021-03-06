<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">

    <h1>
        <span class="glyphicon glyphicon-gift" aria-hidden="true"></span>
        Product : <?= Html::encode($this->title) ?>
    </h1>

    <hr/>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Create Another Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'short_description',
            'value',
            [
                'label' => \Yii::t('app', 'Category'),
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->category_id) {
                        return Html::encode($model->category->name);
                    } else {
                        return null;
                    }
                }
            ],

            'description:raw',
            'valid_date_start:appDate',
            'valid_date_end:appDate',
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
