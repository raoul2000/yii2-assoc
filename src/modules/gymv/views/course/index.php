<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
//$this->registerCss(file_get_contents(__DIR__ . '/dashboard.css'));
?>
<div id="member">
    <h1>Cours</h1>
    <hr/>

    <?php  //echo $this->render('_search', ['model' => $searchModel, 'products' => $products]); ?> 

    <?php 
        // TODO: replace grid filter with selectize input to choose a course product ...
        Pjax::begin(); 
    ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'filterModel' => false,
            'columns' => [
                [
                    'attribute' => 'product_id',
                    'label'     => 'Product',
                    'filter'    => $products,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) {
                        return Html::a('<span class="glyphicon glyphicon-gift" aria-hidden="true"></span> '
                                . Html::encode($model->product->name),
                            ['/product/view','id'=>$model->product_id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view product')]
                        );
                    }
                ],                
                [
                    'attribute' => 'to_contact_id',
                    'label'     => 'Beneficiary',
                    'filter'    => false,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column)  {
                        return Html::a('<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                . Html::encode($model->toContact->longName),
                            ['/contact/view','id'=>$model->to_contact_id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view contact')]
                        );
                    }
                ],                
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
