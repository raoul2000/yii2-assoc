<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs(file_get_contents(__DIR__ . '/_address-edit.js'), View::POS_READY, 'address-edit');
?>
<div class="address-form">
    <h3>
        <span class="glyphicon glyphicon-home" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Address') ?>
        <small class="wizard-step"><?= \Yii::t('app', 'step') ?> 2/5</small>
    </h3>

    <hr/>

    <?php if (count($contactsSameAddress) != 0): ?>
        <div class="alert alert-warning">
            <?= \Yii::t('app', 'This address is also used by') ?> 
            <?php 
                $contactLink = [];
                foreach ($contactsSameAddress as $contactPerAdress) {
                    $contactLink[] = Html::a(
                        '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                            . Html::encode($contactPerAdress->longName),
                        ['/contact/view', 'id' => $contactPerAdress->id],
                        ['target' => '_blank', 'title' => \Yii::t('app', 'open in a new window')]
                    );
                }
                echo implode(', ', $contactLink);
            ?>.<br/>
            <?= \Yii::t('app', 'If you change information below it will affect all other contacts linked to this address.') ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($model->id)) :?>
        <p>
            <?= Html::button(
                \Yii::t('app', 'Update This Address'),
                [
                    'id' => 'btn-update-address',
                    'class' => 'btn btn-primary'
                ]
            )?>
            <?= Html::button(
                \Yii::t('app', 'Create a New Address'),
                [
                    'id' => 'btn-reset-form',
                    'class' => 'btn btn-success'
                ]
            )?>
        </p>            
    <?php endif ?>

    <?php $form = ActiveForm::begin(['id' => 'address-edit-form']); ?>
        <?= Html::hiddenInput('readonly', (!empty($model->id) ? true : false), [ 'id' => 'read-only-form']) ?>
        <?= Html::hiddenInput('action', '', [ 'id' => 'action']) ?>
        <?php if ($model->hasErrors()) {
            echo $form->errorSummary($model);
        }?>

        <?= $form->field($model, 'line_1')
            ->textInput([
                'maxlength'    => true, 
                'autocomplete' => 'off', 
                'placeholder'  => \Yii::t('app', 'Enter the address here ...'),
            ])
            ->label(\Yii::t('app', 'address'))
        ?>

        <?= $form->field($model, 'line_2')
            ->textInput([
                'maxlength'    => true, 
                'autocomplete' => 'off', 
                'placeholder'  => \Yii::t('app', 'address extra information ..')
            ])
            ->label(false)
        ?>

        <?= $form->field($model, 'line_3')
            ->textInput([
                'maxlength'    => true, 
                'autocomplete' => 'off', 
                'placeholder'  => \Yii::t('app', 'address extra information ..')
            ])
            ->label(false)
        ?>

        <div class="row">
            <div class="col-sm-2">
                <?= $form->field($model, 'zip_code')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?> 
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'city')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>        
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'country')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>        
            </div>
        </div>
        <hr/>
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
