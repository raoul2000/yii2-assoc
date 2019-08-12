<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\BankAccount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bank-account-form">

    <?php $form = ActiveForm::begin(); ?>

        <?php if ( ! isset($contact) ):?>
            <div class="form-group">
                <label class="control-label" for="contact_id-selectized">
                    <?= Html::encode($model->getAttributeLabel('contact_id')) ?>
                </label>
                <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                    'name' => Html::getInputName($model, 'contact_id'),
                    'value' => $model->contact_id,
                    'id' => 'contact_id-selectized',
                    'items' => $contacts,
                    'clientOptions' => [
                        // ...
                    ],
                ]); ?>
            </div>    
        <?php endif; ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

        <?= $form->field($model, 'initial_value')->textInput(['maxlength' => true, 'autocomplete'=> 'off']) ?>
        
        <hr/>
        
        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            <?= Html::a(\Yii::t('app', 'Cancel'), ['/bank-account/index'], ['class' => 'btn btn-default']) ?>            
        </div>

    <?php ActiveForm::end(); ?>

</div>
