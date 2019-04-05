<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionPack */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transaction-pack-form">

    <?php $form = ActiveForm::begin(); ?>

        <?php if ( isset($bankAccounts) && count($bankAccounts) !== 0): ?>
            <?= $form->field($model, 'bank_account_id')->listBox($bankAccounts, ['size'=>1])?>
        <?php endif; ?>
        
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'type')->textInput() ?>

        <?= $form->field($model, 'reference_date')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
