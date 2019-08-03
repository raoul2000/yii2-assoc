<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1>
        <span class="glyphicon glyphicon-th" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>
    
    <hr/>

    <p>
        <?= Html::a('Create Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>    
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                [
                    'attribute' => 'contact_id',
                    'filter'    => $contacts,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($contacts) {
                        if (isset($model->contact_id)) {
                            return Html::a(
                                Html::encode($contacts[$model->contact_id]),
                                ['contact/view','id'=>$model->contact_id],
                                [ 'data-pjax' => 0 ]
                            );
                        } else {
                            return null;
                        }
                    }
                ],
    
                'type',
                'name',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['nowrap' => 'nowrap']
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
