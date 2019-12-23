<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

?>
<div id="member">
    <h1>Cours</h1>
    <hr/>

    <?php  
        echo $this->render(
            '_search',
            [
                'searchModel' => $searchModel,
                'products' => $products
            ]
        ); 
    ?> 

    <?php if($selectedProduct): ?>
        <div class="alert alert-info" role="alert">
            <b>
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                <?= Html::encode($selectedProduct->name); ?>
            </b>
            - <?= Html::encode($selectedProduct->short_description); ?>        
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
                    'label'     => \Yii::t('app', 'Member Name'),
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
                ],                
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>