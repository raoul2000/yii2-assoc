<?php

use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;
?>

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

