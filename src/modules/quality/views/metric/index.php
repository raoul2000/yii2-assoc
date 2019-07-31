<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
$this->title = 'Quality Check : ' . $pageSubHeader;

$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['/contact/index']];
$this->params['breadcrumbs'][] = 'Quality Check';
\yii\web\YiiAsset::register($this);

?>
<div class="quality-contact-view">

    <h1>
        <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> 
        Quality Check
        <small> <?= Html::encode($pageSubHeader) ?></small>
    </h1>
    <hr/>

    <div class="row">
        <div class="col-sm-3">
            <div class="list-group">
                <a href="#" class="list-group-item active">
                    <h4 class="list-group-item-heading">Analyse</h4>
                    <p class="list-group-item-text"><small>recherche des incohérences potentielles</small></p>
                </a>
                <a href="#" class="list-group-item">
                    <h4 class="list-group-item-heading">Similarity</h4>
                    <p class="list-group-item-text"><small>Valeurs presque identiques</small></p>
                </a>
            </div>             
        </div>
        <div class="col-sm-9">
            
            <?= GridView::widget([
                'dataProvider' => $provider,
                'showHeader' => false,
                'tableOptions' => ['class' => 'table table-striped table-hover'],
                'summary' => '',
                'rowOptions' => function ($model) {
                    if ($model['value'] != 0) {
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
                                return Url::to([ 'view-data', 'id' => $model['id']]);
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
    </div>

</div>
