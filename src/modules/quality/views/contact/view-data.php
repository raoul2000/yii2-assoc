<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = "Détail";
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['/contact/index']];
$this->params['breadcrumbs'][] = ['label' => 'Quality', 'url' => ['/quality/contact/index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="quality-contact-data-view">

    <h1>
        <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> 
        Quality Check
        <small>Dataset</small>
    </h1>
    <hr/>
    <div class="alert alert-info" role="alert">
        <?= $label ?>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            'firstname',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action == 'view') {
                        return Url::to([
                            '/contact/view',
                            'id' => $model->id
                        ]);
                    }
                }
            ]            
        ],
    ]);
    ?>    
</div>