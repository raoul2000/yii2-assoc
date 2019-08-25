<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Attachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attachment-form">

    <?php $form = ActiveForm::begin(); ?>

        <?php if ($model->hasErrors()) {
            echo $form->errorSummary($model);
        }?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'category_id')->textInput(['maxlength' => true]) ?>

        <hr/>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>


    <?php ActiveForm::end(); ?>

</div>
