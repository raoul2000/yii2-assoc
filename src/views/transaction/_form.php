<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Transaction */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transaction-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'from_account_id')->listBox($bankAccounts, ['size'=>1])?>

        <?= $form->field($model, 'to_account_id')->listBox($bankAccounts, ['size'=>1])?>    

        <?= $form->field($model, 'value')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'reference_date')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>

        <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'is_verified')->checkbox() ?>
        
        <?php
        if ($model->isNewRecord == true && isset($order) == false) {
            echo $form->field($model, 'initial_product_id')->listBox($products, ['size'=>1]);
            echo $form->field($model, 'initial_product_quantity')->textInput(['autocomplete'=>'off']);
        }
        ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
