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
    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'note',
                    'filter'    => false,
                    'label'     => '',
                    'format'    => 'note'
                ],
                [
                    'attribute' => 'name',
                    'label' => 'Name'
                ],
                [
                    'attribute' => 'firstname',
                    'label' => 'Firstname'
                ],
                'email:email',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'  => '{view}',
                    'contentOptions' => ['nowrap' => 'nowrap'],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'view') {
                            return Url::to([
                                '/contact/view',
                                'id' => $model->id
                            ]);
                        }
                    },
                ],                        
            ],
            ]); 
        ?>    
    <?php Pjax::end(); ?>                

</div>
