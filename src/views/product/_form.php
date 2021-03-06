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
    
        <?php if ($model->hasErrors()) {
            echo $form->errorSummary($model);
        }?>

        <div class="row">
            <div class="col-sm-6">
            
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=> 'off']) ?>
                
                <?= $form->field($model, 'short_description')->textInput(['maxlength' => true, 'autocomplete'=> 'off']) ?>

                <?= $form->field($model, 'value')->textInput(['maxlength' => true, 'autocomplete'=> 'off']) ?>

                <?= $form->field($model, 'category_id')->listBox($categories, [
                    'size'=>1,
                    'prompt' => \Yii::t('app', 'select a category ...')
                ])?>                

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

                <div class="panel panel-default">
                    <div class="panel-heading">Validity Date Range</div>
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form
                                    ->field($model, 'valid_date_start')
                                    ->textInput(['maxlength' => true, 'autocomplete'=> 'off', 'placeholder' => \Yii::t('app', 'ex: 30/01/2019') ])
                                    ->label('start') 
                                ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form
                                    ->field($model, 'valid_date_end')
                                    ->textInput(['maxlength' => true, 'autocomplete'=> 'off', 'placeholder' => \Yii::t('app', 'ex: 31/12/2020') ])
                                    ->label('end') 
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <?= $form->field($model, 'description')->widget(Widget::className(), [
                        'settings' => [
                            'lang' => 'fr',
                            'minHeight' => 300,
                            'plugins' => [
                                'fullscreen',
                            ]
                        ],
                    ]);
                ?>        
            </div>
        </div>

        <hr/>
        
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
