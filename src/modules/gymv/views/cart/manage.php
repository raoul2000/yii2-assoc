<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */

$formName = "cart-manager-form";
$jsScript=<<<EOS
    $('#cart-manager-container').on('click', (ev) => {
        ev.stopPropagation();
        ev.preventDefault();
        if(ev.target.dataset.action) {
            document.getElementById('cart-action').value = ev.target.dataset.action;
            if( ev.target.dataset.index) {
                document.getElementById('cart-index').value = ev.target.dataset.index;
            }
            document.forms['{$formName}'].submit();
        }
    });
EOS;

$this->registerJs($jsScript, View::POS_READY, 'cart-manager');

?>
<div id="cart-manager-container" class="cart-check-out">
    <h1>cart Manager</h1>

    <?php $form = ActiveForm::begin(['options' => [ "name" => $formName]]); ?>
        <?= Html::hiddenInput('action','', ["id" => "cart-action"]) ?>
        <?= Html::hiddenInput('index','', ["id" => "cart-index"]) ?>

        <h2>Orders</h2>
        <hr>
        <?= Html::button('add order', ['class' => 'btn btn-default', 'data-action' => 'add-order']) ?>
        
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
                            <?= Html::button('remove', ['class' => 'btn btn-default', 'data-action' => "remove-order", 'data-index' => $index]) ?>
                        </td>
                    </tr>        
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Transactions</h2>
        <hr>
        <?= Html::button('add transaction', ['class' => 'btn btn-default', 'data-action' => 'add-transaction']) ?>

        <table class="table table-condensed table-hover">
            <tbody>
                <?php  foreach ($transactions as $index => $transaction): ?>
                    <tr>
                        <td>
                            <?= $form->field($transaction, "[$index]from_account_id")->listBox($bankAccounts, ['size'=>1])?>
                        </td>
                        <td>
                            <?= $form->field($transaction, "[$index]to_account_id")->listBox($bankAccounts, ['size'=>1])?>
                        </td>
                        <td>
                            <?= $form->field($transaction, "[$index]value")->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
                        </td>
                        <td>
                            <?= $form->field($transaction, "[$index]reference_date")->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
                        </td>
                        <td>
                            <?= $form->field($transaction, "[$index]type")->listBox( \app\components\Constant::getTransactionTypes(), ['size'=>1])?>
                        </td>
                        <td>
                            <?= $form->field($transaction, "[$index]code")->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
                        </td>
                        <td>
                            <?= Html::button('remove', ['class' => 'btn btn-default', 'data-action' => "remove-transaction", 'data-index' => $index]) ?>
                        </td>
                    </tr>        
                <?php endforeach; ?>
            </tbody>
        </table>
        <?= Html::button('Submit Cart', ['class' => 'btn btn-primary', 'data-action' => "submit"]) ?>
    <?php ActiveForm::end(); ?>

</div>

