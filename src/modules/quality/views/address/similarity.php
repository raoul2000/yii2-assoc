<?php

use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
$this->title = 'Quality Check : Similarity' ;

$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['/address/index']];
$this->params['breadcrumbs'][] = 'Quality Check';
\yii\web\YiiAsset::register($this);

?>
<div class="contact-stat">

    <h1>
        <span class="glyphicon glyphicon-stats" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>

    <hr/>
    
    <table class="table  table-hover table-condensed ">
        <thead>
            <tr>
                <th>score</th>
                <th>first</th>
                <th>second</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datasetItems as $key => $dataItem): ?>
                <?php 
                    $rowClass = $dataItem['match'] > 79 ? 'warning' : '';
                ?>
                <tr class="<?= $rowClass ?>">
                    <td><?= round($dataItem['match'], 2) ?></td>
                    <td><?= Html::encode($dataItem['first']) ?></td>
                    <td><?= Html::encode($dataItem['second']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
