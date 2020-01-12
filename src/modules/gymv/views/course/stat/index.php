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

    <div class="list-group">
        <?= Html::a(
            '<h4 class="list-group-item-heading">Séances vendues par cours</h4>'
                . '<p class="list-group-item-text">
                    Affiche le nombre de séances ayant été vendues pour chaque cours -  
                    filtrage par catégorie de cours.
                </p>',
            ['member-count'],
            ['class' => 'list-group-item']
        )?>
    </div>
</div>
