<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="date-range-form">
    <h1>Date Range</h1>
    <hr/>
    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'start_date')->textInput([ 'autocomplete'=> 'off' ]) ?>

        <?= $form->field($model, 'end_date')->textInput([ 'autocomplete'=> 'off' ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', $redirect_url, ['class' => 'btn btn-default']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>