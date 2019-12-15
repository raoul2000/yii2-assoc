<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contact - Cours';
$this->params['breadcrumbs'][] = ['label' => 'Gymv', 'url' => ['/gymv']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Import'), 'url' => ['/gymv/import/home']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-index">
    <h1>Contact - Cours <small>import</small></h1>
    <hr/>
    <div class="row">
        <div class="col-sm-6">
            <p>
                A partir la la liste des contacts par cours, créé la commande
                du cours et la relie au contact.
            </p>
            <ul>
                <li>encodage UTF-8</li>
                <li><b>contact</b> doit exister (aucune insertion)</li>
                <li>colonnes : </li>
                <ul>
                    <li>col 1 : Nom</li>
                    <li>col 2 : Prénom</li>
                    <li>col 3 : Numéro du cours</li>
                </ul>
            </ul>
            <p>Exemple : <br/>
            <pre>
nom,prenom,cours
BEERT,Nathalie,1
TEHUET,Françoise,1
TIIFROY,Céline,1
ERRENT,Clélia,1
EERIERE,Véronique,2
ITORES,Françoise,2            
            </pre></p>
        </div>
        <div class="col-sm-6">
            <div class="well">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
                    <?= $form->field($model, 'dataFile')->fileInput() ?>
                    <?= Html::submitButton(\Yii::t('app', 'Import'), ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>
