<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
?>
<div class="cart-check-out">
    <h1>cart/check-out</h1>
    <hr/>


    <?php $form = ActiveForm::begin(); ?>

        <table class="table table-condensed table-hover">
            <tbody>
                <?php  foreach ($orders as $index => $order): ?>
                    <tr>
                        <td>
                            <?= $form->field($order, "[$index]product_id")->listBox($products, ['size'=>1])?>
                        </td>
                        <td>
                            <?= $form->field($order, "[$index]value")->textInput(['class' => 'order-value form-control', 'maxlength' => true, 'autocomplete'=> 'off']) ?>
                        </td>
                        <td>
                            <?= $form->field($order, "[$index]from_contact_id")->listBox($contacts, ['size'=>1])?>
                        </td>
                        <td>
                            <?= $form->field($order, "[$index]to_contact_id")->listBox($contacts, ['size'=>1])?>
                        </td>
                        <td>
                            <?= Html::a('remove', ['check-out', 'action' => 'remove-order', 'id' => $index], ['class' => 'btn btn-default']) ?>
                        </td>
                    </tr>        
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php ActiveForm::end(); ?>

</div>

