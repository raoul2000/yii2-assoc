<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ContactRelation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-relation-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">

        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="source_contact_id-selectized">Source Contact</label>
                <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                    'name' => Html::getInputName($model, 'source_contact_id'),
                    'value' => $model->source_contact_id,
                    'id' => 'source_contact_id-selectized',
                    'items' => $contacts,
                    'clientOptions' => [
                        // ...
                    ],
                ]); ?>
            </div>
        </div>


        <div class="col-sm-4">
            <?= $form->field($model, 'type')->textInput() ?>
        </div>


        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="target_contact_id-selectized">Target Contact</label>
                <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                    'name' => Html::getInputName($model, 'target_contact_id'),
                    'value' => $model->target_contact_id,
                    'id' => 'target_contact_id-selectized',
                    'items' => $contacts,
                    'clientOptions' => [
                        // ...
                    ],
                ]); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>