<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionPack */
/* @var $form yii\widgets\ActiveForm */

$userChooseBankAccount = (isset($bankAccounts) && count($bankAccounts) !== 0 && $bankAccount == null);
?>

<div class="transaction-pack-form">

    <?php $form = ActiveForm::begin(); ?>

        <?php if ($userChooseBankAccount): ?>
            <?= $form->field($model, 'bank_account_id')->listBox($bankAccounts, ['size'=>1])?>
        <?php endif; ?>
        
        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'type')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'reference_date')->widget(
            \dosamigos\datepicker\DatePicker::className(), [
                'language' => 'fr',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy',
                    'todayHighlight' => true,
                    'clearBtn' => false,
                    'todayBtn' => true,
                    'enableOnReadonly' => true,
                ]
        ]);?>  

        <hr/>
        
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
