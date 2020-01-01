<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Without course';
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="member">
    <h1><?= Html::encode($this->title) ?> ! </h1>
    <hr/>

    <?php  
    /*
        echo $this->render(
            '_search',
            [
                'searchModel' => $searchModel,
                'products' => $products
            ]
        ); 
    */
    ?> 

    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            //'filterModel'  => $searchModel,
            'columns' => [
                'id',
                'name',
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
