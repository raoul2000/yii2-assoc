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
                    'label' => 'Name',
                    'format' => 'raw',
                    'value'     => function ($model, $key, $index, $column) {
                        return Html::a('<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                . Html::encode(ucfirst($model->name)),
                            ['/contact/view','id'=>$model->id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view contact')]
                        );
                    }                    

                ],
                [
                    'attribute' => 'firstname',
                    'label' => 'Firstname'
                ],
                'email:email',
            ],
            ]); 
        ?>    
    <?php Pjax::end(); ?>                

</div>
