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
        <div class="row">
            <div class="col-lg-3">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'firstname')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <?php 
                    //echo $form->field($model, 'birthday')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]); 
                ?>
                <?= $form->field($model, 'birthday')->widget(
                    \dosamigos\datepicker\DatePicker::className(), [
                        'language' => 'fr',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd/mm/yyyy',
                            'todayHighlight' => true,
                            'clearBtn' => true,
                            'enableOnReadonly' => true,
                        ]
                ]);?>

            </div>
        </div>

        <?= $form->field($model, 'gender')->radioList(['1' => 'Male', '2' => 'Female', '0' => 'don\'t know']) ?>
        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
               <?= $form->field($model, 'phone_1')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'phone_2')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
            </div>
        </div>

        <?= $form->field($model, 'note')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
        
        <?php if ($model->isNewRecord): ?>
            <h2>Attachment</h2>
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
