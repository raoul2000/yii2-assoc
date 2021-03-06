<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Séances vendues par Cours';
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
        echo $this->render(
            '_filter-category',
            [
                'category_filter' => $category_filter,
                'categoryOptions' => $categoryOptions
            ]
        ); 
    ?> 
    <div class="alert alert-info">
    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
        Nombre total de participants : <b><?= $memberCount ?></b>
    </div>
    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'name',
                    'label'     => \Yii::t('app', 'Course Name'),
                    'format'    => 'raw',
                    'enableSorting' => false,
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
