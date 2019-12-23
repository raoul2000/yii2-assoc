<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;


$this->title = \Yii::t('app', 'Members');
$this->params['breadcrumbs'][] = ['label' => 'GymV', 'url' => ['/gymv/dashboard/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="member">
    <h1><?= Html::encode($this->title) ?></h1>
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
