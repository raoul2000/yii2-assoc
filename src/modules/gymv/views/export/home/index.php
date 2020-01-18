<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Export';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-index">
<h1><?= Html::encode($this->title) ?> <small>Données adhérent/cours</small></h1>
    <hr/>
    <p>
        Export pour la saison courante. Chaque ligne contient les colonnes suivantes :
        <ul>
            <li>unité : toujours égal à 1</li>
            <li>adhérent : identifiant anonymisé de l"adhérent</li>
            <li>date de naissance de l'adhérent</li>
            <li>age de l"adhérent</li>
            <li>Genre de l'adhérent (Homme ou Femme)</li>
            <li>Code postal de l'adhérent</li>
            <li>nom du cours auquel l'adhérent est inscrit</li>
            <li>Catégorie du cours auquel l'adhérent est inscrit</li>
        </ul>
        L'export contient une ligne par adhérent et par cours. Si par exemple un adhérent est inscrit à 3 cours,
        trois lignes seront exportées.
    </p>

    <p>
        <?= Html::a(
            'Exporter les données', 
            ['/gymv/export/home/index', 'action' => 'export'], 
            ['class' => 'btn btn-primary']) 
        ?>    
    </p>
</div>
