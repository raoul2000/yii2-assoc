<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="address-form">

<?php $form = ActiveForm::begin(); ?>

    <?php if ($model->hasErrors()) {
        echo $form->errorSummary($model);
    }?>

    <?= $form->field($model, 'line_1')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

    <?= $form->field($model, 'line_2')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

    <?= $form->field($model, 'line_3')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

    <?= $form->field($model, 'zip_code')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

    <?= $form->field($model, 'country')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

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
