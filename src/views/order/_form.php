<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'product_id')->listBox($products, ['size'=>1])?>

        <?= $form->field($model, 'value')->textInput(['maxlength' => true, 'autocomplete'=> 'off']) ?>
        
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'from_contact_id')->listBox($contacts, ['size'=>1])?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'to_contact_id')->listBox($contacts, ['size'=>1])?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Validity Date Range</div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <?= $form
                            ->field($model, 'valid_date_start')
                            ->textInput(['maxlength' => true, 'autocomplete'=> 'off', 'placeholder' => 'ex: 30/01/2019' ])
                            ->label('start') 
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form
                            ->field($model, 'valid_date_end')
                            ->textInput(['maxlength' => true, 'autocomplete'=> 'off', 'placeholder' => 'ex: 31/12/2020' ])
                            ->label('end') 
                        ?>
                    </div>
                </div>

            </div>
        </div>

        <hr/>
        
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
