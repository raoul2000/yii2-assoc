<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app', 'Addresses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="address-index">

    <h1>
        <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>

    <hr/>
    
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(\Yii::t('app', 'Create Address'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            '<span class="glyphicon glyphicon-stats" aria-hidden="true"></span> ' . \Yii::t('app', 'Statistics'), 
            ['stat/address'], 
            ['class' => 'btn btn-default',  'data-pjax'=>0]
        )?>
        <?= Html::a(
            '<span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> ' .\Yii::t('app', 'Quality'), 
            ['quality/address', 'tab' => 'analysis'], 
            ['class' => 'btn btn-default',  'data-pjax'=>0]
        )?>
    </p>

    <?= GridView::widget([
        'tableOptions' 		=> ['class' => 'table table-hover table-condensed'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'note',
                'filter'    => false,
                'label'     => '',
                'format'    => 'note'
            ],
            'line_1',
            'line_2',
            'zip_code',
            'city',
            'country',
            //'note',
            //'created_at',
            //'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['nowrap' => 'nowrap']
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
