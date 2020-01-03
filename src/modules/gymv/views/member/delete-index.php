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
            ['stat/address'], 
            ['class' => 'btn btn-default',  'data-pjax'=>0]
        )?>
    </p>    
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
            ],
            ]); 
        ?>    
    <?php Pjax::end(); ?>                

</div>
