<?php

use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = 'Statistics';
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="contact-stat">

    <h1>
        <span class="glyphicon glyphicon-stats" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>

    <hr/>
    
    <div class="row">
        <div class="col-xs-3">
            <?= $countPerson ?>
        </div>
    </div>
    <?php
        echo Highcharts::widget([
            'options' => [
                'chart' => [
                    'type' => 'column'
                ],
                'title' => ['text' => 'Répartition Par Age'],
                'xAxis' => [
                    'categories' => range(0,100),
                    'crosshair' => true
                ],
                'yAxis' => [
                    'min' => 0,
                    'title' => ['text' => 'nombre de personnes']
                ],
                'plotOptions' => [
                    'column' => [
                        'pointPadding' => 0.2,
                        'borderWidth' => 0
                    ]
                ],
                'series' => [
                    ['name' => 'homme', 'data' => $serieMan],
                    ['name' => 'Femme', 'data' => $serieWom]
                ]
            ]
        ]);
    ?>
</div>
