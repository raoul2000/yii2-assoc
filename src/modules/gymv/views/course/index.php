<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
//$this->registerCss(file_get_contents(__DIR__ . '/dashboard.css'));
?>
<div id="member">
    <h1>Adhérents</h1>
    <hr/>

    <?php 
        Pjax::begin(); 
    ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'product_id',
                    'label'     => 'Product',
                    //'filter'    => $products,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($products) {
                        return Html::a('<span class="glyphicon glyphicon-gift" aria-hidden="true"></span> '
                                . Html::encode($products[$model->product_id]),
                            ['product/view','id'=>$model->product_id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view product')]
                        );
                    }
                ],
                [
                    'attribute' => 'to_contact_id',
                    'label'     => 'Beneficiary',
                    'filter'    => $contacts,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($contacts) {
                        return Html::a('<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                . Html::encode($contacts[$model->to_contact_id]),
                            ['contact/view','id'=>$model->to_contact_id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view contact')]
                        );
                    }
                ],
                'valid_date_start:appDate',
                'valid_date_end:appDate',
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
