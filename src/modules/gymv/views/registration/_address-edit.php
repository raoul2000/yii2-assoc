<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="address-form">
    <h3>
        <span class="glyphicon glyphicon-home" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Address') ?>
    </h3>

    <hr/>

    <?php $form = ActiveForm::begin(); ?>

        <?php if ($model->hasErrors()) {
            echo $form->errorSummary($model);
        }?>

        <?= $form->field($model, 'line_1')
            ->textInput([
                'maxlength'    => true, 
                'autocomplete' => 'off', 
                'placeholder'  => \Yii::t('app', 'Enter the address here ...')
            ])
            ->label(\Yii::t('app', 'address'))
        ?>

        <?= $form->field($model, 'line_2')
            ->textInput([
                'maxlength'    => true, 
                'autocomplete' => 'off', 
                'placeholder'  => \Yii::t('app', 'address extra information ..')
            ])
            ->label(false)
        ?>

        <?= $form->field($model, 'line_3')
            ->textInput([
                'maxlength'    => true, 
                'autocomplete' => 'off', 
                'placeholder'  => \Yii::t('app', 'address extra information ..')
            ])
            ->label(false)
        ?>

        <div class="row">
            <div class="col-sm-2">
                <?= $form->field($model, 'zip_code')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?> 
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'city')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>        
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'country')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>        
            </div>
        </div>
        <hr/>
        <div class="form-group">
            <?= Html::a(
                '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> ' . \Yii::t('app', 'Previous'),
                ['address-search'],
                ['class' => 'btn btn-primary']
            )?>

            <?= Html::submitButton(
                \Yii::t('app', 'Next') . ' <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>',
                ['class' => 'btn btn-primary']
            )?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
