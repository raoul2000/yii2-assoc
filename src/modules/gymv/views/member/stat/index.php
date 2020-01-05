<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = \Yii::t('app', 'Statistics');
$this->params['breadcrumbs'][] = ['label' => 'Members', 'url' => ['/gymv/member/home']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="member-state">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr/> 
    <ul>
        <li>
            <?= Html::a('course purchased', ['course-purchased']) ?>
        </li>
        <li>
            <?= Html::a('Licensiée la saison 2018-2019 mais pas la saison actuelle', ['diff-period1']) ?>
        </li>
    </ul>
</div>
