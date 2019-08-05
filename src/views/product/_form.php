<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use vova07\imperavi\Widget;
use dosamigos\datepicker\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>


    <div class="row">
        <div class="col-sm-6">
        
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=> 'off']) ?>

            <?= $form->field($model, 'value')->textInput(['maxlength' => true, 'autocomplete'=> 'off']) ?>

            <?php
            /*
             $form->field($model, 'valid_date_start')->widget(DateRangePicker::className(), [
                'attributeTo' => 'valid_date_end',
                'form' => $form, // best for correct client validation
                'language' => 'fr',
                'size' => 'lg',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy',
                    'todayHighlight' => true,
                    'clearBtn' => true,
                    'enableOnReadonly' => true,
                ]
            ]); 
            */
            ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'valid_date_start')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'valid_date_end')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
                </div>
            </div>


        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'description')->widget(Widget::className(), [
                    'settings' => [
                        'lang' => 'fr',
                        'minHeight' => 200,
                        'plugins' => [
                            'fullscreen',
                        ]
                    ],
                ]);
            ?>        
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
