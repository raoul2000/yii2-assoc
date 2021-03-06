<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="contact-form">

    <h3>
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Contact') ?>
        <small class="wizard-step"><?= \Yii::t('app', 'step') ?> 1/5</small>
    </h3>

    <hr/>

    <?php $form = ActiveForm::begin(); ?>
    
        <?php if ($model->hasErrors()) {
            echo $form->errorSummary($model);
        }?>
        <?php if (!empty($model->id)) :?>
            <div class="alert alert-info">
                <?= \Yii::t('app', 'This Contact is already registered in the system. You can verify the informations and correct any error if needed.') ?><br/>
                <?= Html::a(\Yii::t('app', 'view this contact'), ['/contact/view', 'id' => $model->id], ['target' => '_blank']) ?>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <?= \Yii::t('app', 'This person is not already registered : you are creating a new entry ...') ?>
            </div>
        <?php endif; ?>
    
        <div class="row">
            <div class="col-sm-6">
                <?= $form
                    ->field($model, 'name')
                    ->textInput([
                        'maxlength'    => true, 
                        'autocomplete' => 'off', 
                        'readonly'     => true
                    ]) 
                ?>
            </div>
            <div class="col-sm-6">
                <?= $form
                    ->field($model, 'firstname')
                    ->textInput([
                        'maxlength'    => true, 
                        'autocomplete' => 'off',
                        'readonly'     => !empty($model->id)
                    ])
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'birthday')->widget(
                    \dosamigos\datepicker\DatePicker::className(), [
                        'language' => 'fr',
                        'options' => [
                            'placeholder' => \Yii::t('app', 'ex: 30/01/2019'),
                            'autocomplete'=> 'off'
                        ],
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd/mm/yyyy',
                            'todayHighlight' => true,
                            'clearBtn' => true,
                            'enableOnReadonly' => true,
                        ]
                ]);?>   
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'gender')->radioList(['1' => 'Male', '2' => 'Female', '0' => 'don\'t know'],
                [
                    'class' => 'radio',
                    'itemOptions' => [
                        'style' => 'display: inline-block'
                    ]
                ]) ?>      
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'autocomplete'=> 'off', 'placeholder' => \Yii::t('app', 'ex: john@gmail.com') ]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'phone_1')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
                <?= $form->field($model, 'phone_2')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>   
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'note')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
            </div>
        </div>
        
        <hr/>

        <div class="form-group">
            <?= Html::a(
                '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> ' . \Yii::t('app', 'Previous'),
                ['contact-search'],
                ['class' => 'btn btn-primary']
            )?>
            <?= Html::submitButton(
                \Yii::t('app', 'Next') . ' <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>',
                ['class' => 'btn btn-primary']
            )?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
