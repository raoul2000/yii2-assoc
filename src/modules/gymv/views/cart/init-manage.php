<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */

?>
<div>
    <h1>Init Cart Manager</h1>
    <hr/>
    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($order, 'from_contact_id')->listBox($contacts, ['size'=>1])?>    

        <?= $form->field($order, 'to_contact_id')->listBox($contacts, ['size'=>1]) ?>  

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
