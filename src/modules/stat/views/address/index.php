<?php

use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = 'Statistics';
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['/address/index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="contact-stat">

    <h1>
        <span class="glyphicon glyphicon-stats" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>

    <hr/>
    
    <?php
        echo Highcharts::widget([
            'options' => [
                'chart' => [
                    'type' => 'pie'
                ],
                'title' => ['text' => 'Répartition Géographique'],
                'plotOptions' => [
                    'pie'=> [
                        'allowPointSelect'=> true,
                        'cursor'=> 'pointer',
                        'dataLabels'=> [
                          'enabled'=> false
                        ],
                        'showInLegend'=> true
                      ]
                ],
                'series' => [
                    $serie
                ]
            ]
        ]);
    ?>
</div>
