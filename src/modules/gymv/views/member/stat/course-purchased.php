<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = \Yii::t('app', 'Course purchased');
$this->params['breadcrumbs'][] = ['label' => 'Members', 'url' => ['/gymv/member/home']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Statistics'), 'url' => ['/gymv/member/stat']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="member">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr/> 

    <div class="row">
        <div class="col-xs-3">
            <p>
                Ce graphique met en correspondance le nombre de cours achetés avec le nombre d'adhérents ayant achetés cette 
                quantité de cours.
            </p>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Cours achetés</th>
                        <th>Total Adhérents</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $key => $value) :?>
                        <tr>
                            <td><?= $value['order_count'] ?></td>
                            <td><?= $value['total']  ?> </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        
        </div>
        <div class="col-xs-9">
            <?php
                echo miloschuman\highcharts\Highcharts::widget([
                    'options' => [
                        'chart' => [
                            'type' => 'pie'
                        ],
                        'title' => ['text' => $title],
                        'subtitle' => [
                            'text' => $subTitle
                        ],
                        'credits' => [
                            'enabled' => false
                        ],
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
    </div>

</div>
