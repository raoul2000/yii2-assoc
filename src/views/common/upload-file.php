<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="contact-form">
    <h1>Upload File</h1>
    <hr/>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= $form->field($model, 'category_id')->textInput([ 'autocomplete'=> 'off' ]) ?>
        
        <?= $form->field($model, 'note')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
        
        <?= $form->field($model, 'file[]')->fileInput(['multiple' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', $redirect_url, ['class' => 'btn btn-default']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
