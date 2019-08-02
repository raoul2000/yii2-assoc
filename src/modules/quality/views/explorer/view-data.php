<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Product */


$this->title = 'Quality Check (focus) : ' . $pageSubHeader;
?>
<div class="quality-contact-data-view">
    <h1>
        <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> 
        Quality Check
        <small><?= Html::encode($pageSubHeader) ?> Focus</small>
    </h1>
    <hr/>
    <div class="alert alert-info" role="alert">
        <?= $label ?>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => array_merge(
            $dataColumnNames,
            [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'urlCreator' => function ($action, $model, $key, $index) use ($viewModelRoute) {
                        if ($action == 'view') {
                            return Url::to([
                                $viewModelRoute,
                                'id' => $model->id
                            ]);
                        }
                    }
                ]
            ]
        )
    ]);?>    
</div>
