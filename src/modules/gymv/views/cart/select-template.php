<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */

?>
<div>
    <h1>Select Template</h1>
    <hr/>
    <?php if (count($templateNames) == 0): ?>
        <div class="alert alert-info" role="alert">
            You don't have any template available at the moment.        
        </div>    
    <?php else: ?>
        <?php if ($notEmptyCartWarning): ?>    
            <div class="alert alert-warning" role="alert">
                It seems your cart is not empty. If you choose to apply a template, your existing cart will be reset.
            </div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(); ?>

            <div class="form-group">
                <?= Html::dropDownList('template-name', null, $templateNames, [
                    'size'=>1,
                    'prompt' => 'select ...',
                    'class' => 'form-control'
                ])?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    <?php endif; ?>
</div>
