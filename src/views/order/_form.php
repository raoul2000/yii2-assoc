<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<?php if (isset($transaction) && $transaction !== null) : ?>
    <?= Html::a('go back to transaction', ['transaction/view', 'id' => $transaction->id], ['class' => 'btn btn-default']) ?>
<?php endif; ?>
<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'product_id')->listBox($products, ['size'=>1])?>

        <?= $form->field($model, 'value')->textInput(['maxlength' => true, 'autocomplete'=> 'off']) ?>
        
        <?php
        if ($model->getIsNewRecord()) {
            echo $form->field($model, 'initial_quantity')->textInput(['autocomplete'=>'off']);
        }
        ?>
        <?= $form->field($model, 'contact_id')->listBox($contacts, ['size'=>1])?>

        <?= $form->field($model, 'valid_date_start')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>

        <?= $form->field($model, 'valid_date_end')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
