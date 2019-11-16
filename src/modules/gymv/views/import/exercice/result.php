<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Exercice';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="import-csv">
    <?= $errorMessage ?>
    <?php if (!empty($records)): ?>
    <!--
    <?= VarDumper::dumpAsString($records) ?></td>
    -->
        <h2>Records</h2>
        <hr/>
        <table class="table">
            <tbody>
                <?php foreach($records as $offset => $record): ?>
                    <tr>
                        <td><?= $offset ?></td>
                        <td><?= $record['action'] ?></td>
                        <td><pre><?php
                            echo VarDumper::export($record['data']);
                        ?></pre></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
