<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = "Quality Check";
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['/contact/index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quality-contact-view">

    <h1>
        <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
        <small>Contact</small>
    </h1>
    <hr/>

    <?= GridView::widget([
        'dataProvider' => $provider,
        'showHeader' => false,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'label',
                'format' => 'raw'
            ],
            'value',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action == 'view') {
                        return Url::to([
                            'contact/view-data',
                            'id' => $model['id']
                        ]);
                    }
                }
            ]            
        ],
    ]);
    ?>    
</div>