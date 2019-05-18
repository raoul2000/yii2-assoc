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
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="from_account-selectized">Source Account</label>
                    <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                        'name' => Html::getInputName($model, 'from_account_id'),
                        'value' => $model->from_account_id,
                        'id' => 'from_account-selectized',
                        'items' => $bankAccounts,
                        'clientOptions' => [
                            // ...
                        ],
                    ]); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="to_account-selectized">Target Account</label>
                    <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                        'name' => Html::getInputName($model, 'to_account_id'),
                        'id' => 'to_account-selectized',
                        'value' => $model->to_account_id,
                        'items' => $bankAccounts,
                        'clientOptions' => [
                            // ...
                        ],
                    ]); ?>
                </div>
            </div>
        </div>

        <?= $form->field($model, 'value')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'reference_date')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>

        <?= $form->field($model, 'type')->listBox( \app\components\Constant::getTransactionTypes(), ['size'=>1])?>

        <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => 'select a category ...']) ?>

        <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
        
        <?php if (!$model->isNewRecord):?>
            <?= $form->field($model, 'is_verified')->checkbox() ?>
        <?php endif; ?>
        
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
