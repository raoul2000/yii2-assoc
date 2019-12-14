<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Résultat';
$this->params['breadcrumbs'][] = ['label' => 'Gymv', 'url' => ['/gymv']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Import'), 'url' => ['/gymv/import/home']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Contact - cours'), 'url' => ['/gymv/import/course']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="import-csv">
    <h1>Import Result</h1>
    <hr />
    <?= $errorMessage ?>
    <?php foreach($records as $offset => $record): ?>
        <details>
            <summary><?php 
                echo $offset . ' : ' . implode('. ',$record['data']['message'])
            ?></summary>
            <p>
                <pre><?php
                echo VarDumper::export($record['data']);
                ?></pre>                        
            </p>
        </details>
    <?php endforeach; ?>
</div>
