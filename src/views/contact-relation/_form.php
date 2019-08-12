<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\ContactRelation */
/* @var $form yii\widgets\ActiveForm */

$gridViewElementId = 'available-products';
$jsScript=<<<EOS
    document.getElementById('chk-define-date-range').addEventListener('click', ev => {
        debugger;
        const dateRangeInput =  document.getElementById('date-range-input');
        dateRangeInput.querySelectorAll('input').forEach( input => {
            input.value = '';
        });
        dateRangeInput.style.display = ev.target.checked ? 'block' : 'none';
    });
EOS;

//$this->registerJs($jsScript, View::POS_READY, 'contact-relation-form');

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
                <div class="form-group">
                    <label class="control-label" for="type-selectized">Relation Type</label>
                    <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                        'name' => Html::getInputName($model, 'type'),
                        'value' => $model->type,
                        'id' => 'type-selectized',
                        'items' => $contactRelationTypes,
                        'clientOptions' => [
                            // ...
                        ],
                    ]); ?>
                </div>

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

        <!-- div class="form-group">
            <div class="checkbox">
                <label>
                    <input id="chk-define-date-range" type="checkbox"> This relation is valid only for a defined date range
                </label>
            </div>
        </div -->


        <div id="date-range-input" style="display:block;" class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'valid_date_start')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'valid_date_end')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>    
            </div>
        </div>

        <hr/>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
