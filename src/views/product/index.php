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
    
    <?php 
        //Pjax::begin(); 
    ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(\Yii::t('app', 'Create Product'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= \app\components\widgets\DownloadDataGrid::widget() ?>   
    </p>

    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-hover table-condensed'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            'value',
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
        //Pjax::end(); 
    ?>
</div>
