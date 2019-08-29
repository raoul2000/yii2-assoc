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

        <?php if ($model->hasErrors()) {
            echo $form->errorSummary($model);
        }?>

        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label" for="from_account-selectized"><?= \Yii::t('app', 'Source Account') ?></label>
                    <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                        'name' => Html::getInputName($model, 'from_account_id'),
                        'value' => $model->from_account_id,
                        'id' => 'from_account-selectized',
                        'items' =>   $bankAccounts,
                        'clientOptions' => [
                            'placeholder' => \Yii::t('app', 'select a source account')
                        ],
                    ]); ?>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label" for="to_account-selectized"><?= \Yii::t('app', 'Target Account') ?></label>
                    <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                        'name' => Html::getInputName($model, 'to_account_id'),
                        'id' => 'to_account-selectized',
                        'value' => $model->to_account_id,
                        'items' => $bankAccounts,
                        'clientOptions' => [
                            'placeholder' => \Yii::t('app', 'select a target account')
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
                <?= $form->field($model, 'type')->listBox(
                    \app\components\Constant::getTransactionTypes(), 
                    [
                        'size'=> 1,
                        'prompt' => \Yii::t('app', 'select type the type of transaction')
                    ])?>
            </div>
            <div class="col-md-5">
                <?php 
                    //echo $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => 'select a category ...']) 
                ?>
                <div class="form-group">
                    <label class="control-label" for="category-selectized"><?= \Yii::t('app', 'Category') ?></label>
                    <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                        'name' => Html::getInputName($model, 'category_id'),
                        'id' => 'category-selectized',
                        'value' => $model->category_id,
                        'items' => $categories,
                        'clientOptions' => [
                            'create' => true,
                            'placeholder' => \Yii::t('app', 'select or enter a category'),
                        ],
                    ]); ?>
                </div>
            </div>
        </div>

        <?= $form->field($model, 'description')->textInput([
            'maxlength' => true, 
            'autocomplete'=>'off', 
            'placeholder' => \Yii::t('app', 'Enter a description...')
        ])?>
        
        <?php if (!$model->isNewRecord):?>
            <?= $form->field($model, 'is_verified')->checkbox() ?>
        <?php endif; ?>

        <?= $form->field($model, 'tagValues')->widget(\dosamigos\selectize\SelectizeTextInput::className(), [
                // calls an action that returns a JSON object with matched tags
                'loadUrl' => ['query-tags'],
                'options' => ['class' => 'form-control'],
                'clientOptions' => [
                    'plugins' => ['remove_button'],
                    'valueField' => 'name',
                    'labelField' => 'name',
                    'searchField' => ['name'],
                    'create' => true,
                ],
            ])->hint(\Yii::t('app', 'Use commas to separate tags'))
        ?>


        <?php if ($model->isNewRecord): ?>
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span><?= \Yii::t('app', 'Attachment') ?></a></li>
            </ul>
            <div>
                <?= $form->field($uploadForm, 'note')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
                <?= $form->field($uploadForm, 'file')->fileInput() ?>
            </div>
        <?php endif; ?>

        <hr/>
        
        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
