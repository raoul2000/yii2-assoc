<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = \Yii::t('app', 'Statistics');
$this->params['breadcrumbs'][] = ['label' => 'Cours', 'url' => ['/gymv/course/home']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="member-state">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr/> 
    <ul>
        <li>
            <?= Html::a('répartition inscription', ['member-count']) ?>
        </li>
    </ul>
</div>
