<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BankAccount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bank-account-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'contact_id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
