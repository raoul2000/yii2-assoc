<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Category;

/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>
    
        <?php if ($model->hasErrors()) {
            echo $form->errorSummary($model);
        }?>

        <?php if ($model->getIsNewRecord()): ?>
            <?= $form->field($model, 'type')->dropDownList(Category::getTypes(), ['prompt' => \Yii::t('app', 'select a type ...')]) ?>
        <?php else: ?>
            <?= $form->field($model, 'type')->dropDownList(Category::getTypes(), ['disabled' => 'true']) ?>
        <?php endif; ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <hr/>
    
    <?php ActiveForm::end(); ?>

</div>
