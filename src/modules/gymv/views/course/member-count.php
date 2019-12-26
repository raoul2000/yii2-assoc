<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Effectifs Cours';
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="member">
    <h1><?= Html::encode($this->title) ?> </h1>
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
            'columns' => [
                [
                    'attribute' => 'name',
                    'label'     => \Yii::t('app', 'Course Name'),
                    'filter'    => false,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column)  {
                        return Html::a('<span class="glyphicon glyphicon-gift" aria-hidden="true"></span> '
                                . Html::encode($model['name']),
                            [
                                'course/index',
                                'id'=>$model['id'],
                                'OrderSearch[product_id]'=>$model['id'],
                            ],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view detail')]
                        )
                        . '<div style="margin-left:1.2em;font-size: 0.8em;">' . Html::encode($model['short_description']) . '</div>';
                    }
                ],     
                [
                    'attribute' => 'order_count',
                    'filter' => false,
                    'label' => \Yii::t('app', 'Member Count')
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
