<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
/* @var $form yii\widgets\ActiveForm */

$uploadForm = new \app\models\forms\UploadForm();
$uploadMetadataForm = new \app\models\forms\UploadMetadataForm();

?>

<div class="contact-form">

    
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>

    <?= $form->field($uploadMetadataForm, 'description[]')->textInput() ?>
    <?= $form->field($uploadForm, 'file[]')->fileInput() ?>

    <?= $form->field($uploadMetadataForm, 'description[]')->textInput() ?>
    <?= $form->field($uploadForm, 'file[]')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
