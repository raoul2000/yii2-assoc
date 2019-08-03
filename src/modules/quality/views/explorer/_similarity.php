<?php

use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;

?>
<div>
    <?php if (count($colNameOptions) > 1): ?>
        <div class="well">
            <form class="form-inline">
                <div class="form-group">
                    <?= Html::dropDownList('colname', $selectedColName, $colNameOptions, [
                        'size'=>1,
                        'id' => 'colname',
                        'prompt' => 'select ...',
                        'class' => 'form-control'
                    ])?>
                    <button id="btn-show-similarity" type="button" class="btn btn-default">Show Similar</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
    <div>
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
</div>

