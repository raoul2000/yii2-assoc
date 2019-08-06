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

        <?php if (count($configuredDateRanges) != 0): ?>
            <div class="form-group">
                <label class="control-label" for="configured-date-range">Configured Date Range</label>
                <?= Html::listBox('configuredDateRangeId', $model->configuredDateRangeId, $configuredDateRanges, [
                    'id'     => 'configured-date-range',
                    'size'   => 1,
                    'class'  => 'form-control',
                    'prompt' => 'select ...',
                ])?>
            </div>
        <?php endif; ?>

        <?= $form->field($model, 'start_date')->textInput([ 'autocomplete'=> 'off' ]) ?>

        <?= $form->field($model, 'end_date')->textInput([ 'autocomplete'=> 'off' ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?php if (!empty($model->start_date) || !empty($model->end_date)): ?>
                <?= Html::a(
                    'Clear Date Range', 
                    ['date-range', 'clear' => 1, 'redirect_url' => $redirect_url],
                    ['class' => 'btn btn-danger']
                )?>
            <?php endif;?>
            <?= Html::a('Cancel', $redirect_url, ['class' => 'btn btn-default']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
