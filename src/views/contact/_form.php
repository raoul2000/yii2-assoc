<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
/* @var $form yii\widgets\ActiveForm */

$uploadForm = new \app\models\forms\UploadForm();
?>

<div class="contact-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
        
        <?= $form->field($model, 'is_natural_person')->checkbox() ?>

        <?= $form->field($model, 'firstname')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>

        <?= $form->field($model, 'birthday')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>

        <?= $form->field($model, 'note')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
        

        <?php if ($model->isNewRecord): ?>
            <hr/>
            <?= $form->field($uploadForm, 'note')->textInput() ?>
            <?= $form->field($uploadForm, 'file')->fileInput() ?>
            <hr/>
        <?php endif; ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
