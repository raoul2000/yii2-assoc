<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Import';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-index">
    <h1>Product Import</h1>
    <hr/>
    <p>
        Import a set of products from a CSV file. Following requirements must be completed :
        <ul>
            <li>Encoding is UTF-8</li>
            <li>columns : <b>'JOUR', 'LIEUX', 'COURS_NUM','HEURES', 'COURS', 'RESPONSABLES','TELEPHONE','ANIMATEURS','CATEGORY', 'VALUE'</b>- any other named column is ignored</li>
            <li>No extra empty columns</li>
        </ul>
    </p>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        <?= $form->field($model, 'dataFile')->fileInput() ?>
        <?= Html::submitButton('Import', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
</div>
