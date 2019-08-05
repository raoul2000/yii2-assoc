<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Transaction */
/* @var $form yii\widgets\ActiveForm */

$uploadForm = new \app\models\forms\UploadForm();

?>

<div class="transaction-form">

    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-5">
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
            <div class="col-md-5">
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
            <div class="col-md-2">
                <?= $form->field($model, 'value')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
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
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'type')->listBox(\app\components\Constant::getTransactionTypes(), ['size'=>1])?>
            </div>
            <div class="col-md-5">
                <?php 
                    //echo $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => 'select a category ...']) 
                ?>
                <div class="form-group">
                    <label class="control-label" for="category-selectized">Category</label>
                    <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                        'name' => Html::getInputName($model, 'category_id'),
                        'id' => 'category-selectized',
                        'value' => $model->category_id,
                        'items' => $categories,
                        'clientOptions' => [
                            'create' => true
                        ],
                    ]); ?>
                </div>
            </div>
        </div>


        



        <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
        
        <?php if (!$model->isNewRecord):?>
            <?= $form->field($model, 'is_verified')->checkbox() ?>
        <?php endif; ?>

        <?php if ($model->isNewRecord): ?>
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>Attachment</a></li>
            </ul>
            <div>
                <?= $form->field($uploadForm, 'note')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
                <?= $form->field($uploadForm, 'file')->fileInput() ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
