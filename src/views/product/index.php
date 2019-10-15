<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app', 'Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1>
        <span class="glyphicon glyphicon-gift" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>

    <hr/>

    <p>
        <?= Html::a(\Yii::t('app', 'Create Product'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= \app\components\widgets\DownloadDataGrid::widget() ?>   
    </p>

    <?php 
        Pjax::begin(); 
    ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'name',
                    'contentOptions' => ['nowrap' => 'nowrap']
                ],                
                [
                    'attribute' => 'short_description',
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column){
                        return '<small>' . Html::encode($model->short_description) . '</small>';
                    }
                ],                
                [
                    'attribute' => 'value',
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column){
                        return '<b>' . $model->value . '</b>';
                    }
                ],                
                [
                    'attribute' => 'category_id',
                    'label'     => \Yii::t('app', 'Category'),
                    'filter'    => $categories,
                    'value'     => function ($model, $key, $index, $column) use($categories){
                        return $model->category_id != null 
                            ? Html::encode($categories[$model->category_id])
                            : null;  
                    }
                ],
                'valid_date_start:appDate',
                'valid_date_end:appDate',
                /*
                [
                    'attribute' => 'updated_at',
                    'format' => ['date', 'php:d/m/Y H:i']
                ],
                [
                    'attribute' => 'created_at',
                    'format' => ['date', 'php:d/m/Y H:i']
                ],
                */

                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['nowrap' => 'nowrap']
                ],
            ],
        ]); ?>
    <?php 
        Pjax::end(); 
    ?>
</div>
