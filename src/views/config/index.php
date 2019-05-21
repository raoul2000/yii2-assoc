<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $models yii2tech\config\Item[] */
?>
<div class="config">

    <h1>
        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> 
        Configuration 
    </h1>
    <hr/>
    <?php if (count($models) == 0): ?>
        <p>
            <b>Aucun paramètre de configuration disponible</b>
        </p>
    <?php else: ?>
        <?php $form = ActiveForm::begin(); ?>
            <?php foreach ($models as $key => $model): ?>
                <?php
                $field = $form->field($model, "[{$key}]value");
                $inputType = ArrayHelper::remove($model->inputOptions, 'type');
                switch ($inputType) {
                    case 'checkbox':
                        $field->checkbox();
                        break;
                    case 'textarea':
                        $field->textarea();
                        break;
                    case 'dropDown':
                        $field->dropDownList($model->inputOptions['items']);
                        break;
                }
                echo $field;
                ?>
            <?php endforeach;?>

            <div class="form-group">
                <?= Html::a('Restore defaults', ['default'], ['class' => 'btn btn-danger', 'data-confirm' => 'Are you sure you want to restore default values?']); ?>
                &nbsp;
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end(); ?>

    <?php endif; ?>
</div>