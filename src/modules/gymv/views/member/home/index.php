<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = \Yii::t('app', 'Members');
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="member">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>
    <p>
        <?= Html::a(
            '<span class="glyphicon glyphicon-stats" aria-hidden="true"></span> ' . \Yii::t('app', 'Statistics'), 
            ['/gymv/member/stat'], 
            ['class' => 'btn btn-default',  'data-pjax'=>0]
        )?>
    </p>    

    <?= yii\bootstrap\Nav::widget([
        'options' => ['class' =>'nav-tabs'],
        'items' => [
            [
                'label' => \Yii::t('app', 'All members'),
                'encode' => false,
                'url' => ['index', 'tab'=>'all'],
                'active' => $tab == 'all'
            ],
            [
                'label' => \Yii::t('app', 'No Course'),
                'url' => ['index', 'tab'=>'no-course'],
                'active' => $tab == 'no-course'
            ],
        ]
    ]) ?>

    <div style="margin-top:1em;">
        <?php Pjax::begin(); ?>
            <?php if ($tab == 'all' || $tab == 'no-course' || 1 == 1):?>
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
                                return Html::a( Html::encode(ucfirst($model->name)),
                                    ['view','id'=>$model->id],
                                    [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view')]
                                );
                            }                    
                        ],
                        [
                            'attribute' => 'firstname',
                            'label' => 'Firstname'
                        ],
                        'email:email',
                        'order_count'
                    ],
                    ]); 
                ?>    
            <?php endif; ?>    
        <?php Pjax::end(); ?>                
    </div>

</div>
