<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$confirmMessage = \Yii::t('app', 'Are you sure you want to delete the selected template ?');
$noSeledctionMessage = \Yii::t('app', 'no template selected');

$jsScript=<<<EOS
    document.getElementById('btn-delete-template').addEventListener('click', (ev) => {
        if(document.getElementById('template-list').value.length != 0) {
            if(confirm("$confirmMessage")) {
                document.getElementById('template-action').value = 'delete-template';
            } else {
                ev.stopPropagation();
                ev.preventDefault();
            }
        } else {
            alert("$noSeledctionMessage");
            ev.stopPropagation();
        }
    });
EOS;

$this->registerJs($jsScript, View::POS_READY, 'template-selector');
?>
<div>
    <h1>Select Template</h1>
    <hr/>
    <?php if (count($templateNames) == 0): ?>
        <div class="alert alert-info" role="alert">
            You don't have any template available at the moment.        
        </div>    
    <?php else: ?>
        <?php if ($notEmptyCartWarning): ?>    
            <div class="alert alert-warning" role="alert">
                It seems your cart is not empty. If you choose to apply a template, your existing cart will be reset.
            </div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(); ?>
            <?= Html::hiddenInput('action', '', [ 'id' => 'template-action']) ?>
            <div class="form-group">
                <?= Html::dropDownList('template-name', null, $templateNames, [
                    'id' => 'template-list',
                    'size'=>1,
                    'prompt' => 'select ...',
                    'class' => 'form-control'
                ])?>
            </div>

            <div class="form-group">
                <?= Html::submitButton(\Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
                <?= Html::a(\Yii::t('app', 'Cancel'), ['cart/check-out'], ['class' => 'btn btn-default']) ?>
                <?= Html::submitButton(\Yii::t('app', 'Delete'), ['id' => 'btn-delete-template', 'class' => 'btn btn-danger']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    <?php endif; ?>
</div>
