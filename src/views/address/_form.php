<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Address */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="address-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'line_1')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'line_2')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'line_3')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'zip_code')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'city')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'country')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'note')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <hr/>
        
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', $redirect_url, ['class' => 'btn btn-default'])  ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
