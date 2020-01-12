<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Effectifs Cours';
$this->params['breadcrumbs'][] = ['label' => 'Cours', 'url' => ['/gymv/course/home']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Statistics'), 'url' => ['/gymv/course/stat']];
$this->params['breadcrumbs'][] = $this->title;


?>
<div id="member">
    <h1><?= Html::encode($this->title) ?> </h1>
    <hr/>
    <p>
        <?= \app\components\widgets\DownloadDataGrid::widget() ?>      
    </p>
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
                [
                    'attribute' => 'name',
                    'label'     => \Yii::t('app', 'Course Name'),
                    //'filter'    => false,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column)  {
                        return Html::a('<span class="glyphicon glyphicon-gift" aria-hidden="true"></span> '
                                . Html::encode($model['name']),
                            [
                                '/gymv/course/home',
                                'id'=>$model['id'],
                                'OrderSearch[product_id]'=>$model['id'],
                            ],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view detail')]
                        );
                    }
                ],     
                [
                    'attribute' => 'order_count',
                    'filter' => false,
                    'label' => 'Nombre de participants'
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
