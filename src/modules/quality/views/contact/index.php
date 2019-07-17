<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = 'Quality Check';
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
        'rowOptions' => function ($model) {
            if ($model['value'] != 0){
                return ['class' => 'danger'];
            }
        },
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
                        return Url::to([ 'contact/view-data', 'id' => $model['id']]);
                    }
                },
                'buttons'   => [
                    'view' => function ($url, $model, $key) {
                        if ($model['value'] == 0) {
                            return '';
                        } else {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                $url,
                                ['title' => 'view list in a new window', 'data-pjax'=>0, 'target' => '_blank']
                            );
                        }
                    },
                ]
            ]
        ],
    ]);?>        
</div>
