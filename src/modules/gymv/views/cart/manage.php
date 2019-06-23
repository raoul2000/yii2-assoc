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

    const synchProductValues = () => {
        document.querySelectorAll('.orders select[data-product');
        document.querySelectorAll('.orders select[data-product').forEach( copySelectedProductValue );
    };

    const copySelectedProductValue = (sel, overwriteTargetValue) => {
        const targetElement = document.getElementById(sel.dataset.targetId);
        const orderValue = sel.selectedOptions[0].dataset.value;
        if( overwriteTargetValue || targetElement.value.trim().length != 0) {
            targetElement.value = orderValue;
        }
    };

    $('.orders select[data-product]').change( (ev) => {
        //debugger;
        copySelectedProductValue(ev.target, true);
        return;
        const inputValue = document.getElementById(ev.target.dataset.targetId);
        const orderValue = ev.target.selectedOptions[0].dataset.value;
        inputValue.value = orderValue;
        //alert(orderValue);
    });

    $(document).ready( () => {
        // disabled : deserve more work
        //synchProductValues();
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
        
        <table class="table table-condensed table-hover orders">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Value</th>
                    <th>Fournisseur</th>
                    <th>Beneficiaire</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php  foreach ($orders as $index => $order): ?>
                    <tr>
                        <td>
                            <?= $form->field($order, "[$index]product_id")
                                ->listBox($products, [
                                    'size'=>1, 
                                    'data-product' => true,  
                                    'data-target-id' => Html::getInputId($order, "[$index]value"),
                                    'options' => $productOptions
                                ])
                                ->label(false)
                            ?>
                        </td>
                        <td>
                            <?= $form->field($order, "[$index]value")
                                ->textInput(['class' => 'order-value form-control', 'maxlength' => true, 'autocomplete'=> 'off'])
                                ->label(false) 
                            ?>
                        </td>
                        <td>
                            <?= $form->field($order, "[$index]from_contact_id")
                            ->listBox($contacts, ['size'=>1])
                            ->label(false)
                        ?>
                        </td>
                        <td>
                            <?= $form->field($order, "[$index]to_contact_id")
                                ->listBox($contacts, ['size'=>1])
                                ->label(false)
                            ?>
                        </td>
                        <td>
                            <?= Html::button(
                                '<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>', 
                                ['class' => 'btn btn-danger btn-sm', 'data-action' => "remove-order", 'data-index' => $index,
                                'title' => 'remove']
                            ) ?>
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

