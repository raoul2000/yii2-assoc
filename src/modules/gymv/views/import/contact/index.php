<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'I Réseau';
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Import'), 'url' => ['/gymv/import/home']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <h1>Contact Import <small>I Réseau</small></h1>
    <hr/>
    <p>
        Import des contacts licensiés depuis le systèle I Réseau.
    </p>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        <?= $form->field($model, 'dataFile')->fileInput() ?>
        <?= Html::submitButton('Enregistrement', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
</div>


