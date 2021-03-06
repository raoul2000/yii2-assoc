<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = $selectedProduct 
    ? $selectedProduct->name
    : 'Tous les cours';
$this->params['breadcrumbs'][] = $this->title;
    
?>
<div id="member">
    <h1>
        <?= Html::encode($this->title) ?> <small>Participants</small>
    </h1>
    <hr/>

    <div class="row">
        <div class="col-xs-10">
            <?php  
                echo $this->render(
                    '_search',
                    [
                        'searchModel' => $searchModel,
                        'products' => $products
                    ]
                ); 
            ?> 
        </div>
        <div class="col-xs-2">
            <?= Html::a(
                '<span class="glyphicon glyphicon-stats" aria-hidden="true"></span> ' . \Yii::t('app', 'Statistics'), 
                ['/gymv/course/stat'], 
                ['class' => 'btn btn-default',  'data-pjax'=>0]
            )?>
        </div>
    </div>

    <?php if($selectedProduct): ?>
        <div class="alert alert-info" role="alert">
            <b>
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 
                <?= Html::encode($selectedProduct->short_description) ?>
            </b>
            (<?= Html::a(\Yii::t('app', 'detail'), ['/product/view', 'id' => $selectedProduct->id]
                , [ 'title' => \Yii::t('app', 'view course detail')]); ?>)
        </div>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            No course selected
        </div>
    <?php endif; ?>

    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'to_contact_id',
                    'label'     => 'Nom',
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
                    'template'  => '{view}',
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'view') {
                            return Url::to([
                                '/order/view',
                                'id' => $model->id
                            ]);
                        }
                    },
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>'
                                , ['/order/view', 'id' => $model->id]
                                , [
                                    'title' => \Yii::t('app', 'view order'),
                                    'data-pjax' => 0, 
                                ]);
                        },
                    ]
                ],                
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
