<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="user-contact-form">
    <h1>User Contact</h1>
    <hr/>
    <?php if (count($contactNames) === 0): ?>
        <div class="alert alert-warning" role="alert">
            <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
            <b>No Contact available</b><br/>
            Please <?= Html::a('click here', ['contact/create'],['class'=> 'alert-link']) ?> to create a contact.
        </div>        
    <?php else: ?>
        <?php $form = ActiveForm::begin(); ?>

            <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                'name' => Html::getInputName($model, 'contact_id'),
                'value' => $model->contact_id,
                'items' => $contactNames,
                'clientOptions' => [
                    // ...
                ],
            ]); ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Cancel', $redirect_url, ['class' => 'btn btn-default']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    <?php endif;?>
</div>
