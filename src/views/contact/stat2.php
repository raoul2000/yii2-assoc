<?php

use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = 'Stat 2';
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="contact-stat">

    <h1>
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>

    <hr/>
    
    <?php
        echo Highcharts::widget([
            'options' => [
                'chart' => [
                    'type' => 'column',
                    'events' => [
                        'load' => new \yii\web\JsExpression("function () {
                            alert('event.load');
                            var serieMan = this.series[0];
                            var serieWom = this.series[1];
                            $.getJSON('{$datasourceUrl}', function (jsondata) {
                                serieMan.data = jsondata.serieMan;
                                serieWom.data = jsondata.serieWom;
                            });
                        }")
                    ],                    
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
                    ['name' => 'homme', 'data' => []],
                    ['name' => 'Femme', 'data' => []]
                ]
            ]
        ]);
    ?>
</div>
