<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ContactRelation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-relation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'source_contact_id')->textInput() ?>

    <?= $form->field($model, 'target_contact_id')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
