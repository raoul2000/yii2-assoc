<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'I-Réseau';
$this->params['breadcrumbs'][] = ['label' => 'Gymv', 'url' => ['/gymv']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Import'), 'url' => ['/gymv/import/home']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <h1>I-Réseau Import</h1>
    <hr/>
    <div class="row">
        <div class="col-sm-6">
            <p>
                Import des contacts licensiés depuis le système I-Réseau. Cet import permet de créer ou de mettre à jour
                les informations concernant les personnes licenciées : contact, addresse<br/>
                Colonnes : 
                <ul>
                    <li><b>name</b> : nom de famille</li>
                    <li><b>woman_name</b> : nom de jeune fille</li>
                    <li><b>firstname</b> : prénom</li>
                    <li><b>gender</b> : genre (Femme, Homme)</li>
                    <li><b>birthday</b> : date de naissance (format AAAA-MM-JJ)</li>
                    <li><b>license_num</b> : numéro de license (ex : "14010932")</li>
                    <li><b>license_cat</b> : catégorie de license ("Adulte avec Assurance", "Enfant avec Assurance")</li>
                    <li><b>residence</b> : nom de résidence</li>
                    <li><b>locality</b> : ville</li>
                    <li><b>street</b> : nom de rue (ex: "4 AVENUE DU GENERAL DE GAULLE"</li>
                </ul>
            </p>
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


