<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="date-range-form">
    <h1>
        <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> <?= \Yii::t('app', 'Date Range') ?>
    </h1>
    <hr/>
    <?php $form = ActiveForm::begin(); ?>

        <?php if (count($configuredDateRanges) != 0): ?>
            <?= $form
                ->field($model, 'configuredDateRangeId')
                ->listBox(
                    $configuredDateRanges,
                    [
                        'size'=>1,
                        'prompt' => \Yii::t('app', 'select a date range ...')
                    ]
                )
            ?>
        <?php endif; ?>

        <?= $form->field($model, 'start')->textInput([ 'autocomplete'=> 'off' ]) ?>

        <?= $form->field($model, 'end')->textInput([ 'autocomplete'=> 'off' ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?php if (!empty($model->start) || !empty($model->end)): ?>
                <?= Html::a(
                    \Yii::t('app', 'Clear Date Range'), 
                    ['date-range', 'clear' => 1, 'redirect_url' => $redirect_url],
                    ['class' => 'btn btn-danger']
                )?>
            <?php endif;?>
            <?= Html::a(
                \Yii::t('app', 'Cancel'), 
                $redirect_url, 
                ['class' => 'btn btn-default']
            )?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
