<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */

$formName = 'cart-manager-form';
$jsScript=<<<EOS
    /**
     * page action manager
     * Handle all actions emitted by click on element having "data-action" as own attribute
     * or ancestors
     */
    $('#cart-manager-container').on('click', (ev) => {
        const actionEl = ev.target.closest("[data-action]");
        if(actionEl) {
            ev.stopPropagation();
            ev.preventDefault();
            const actionName = actionEl.dataset.action;
            console.log(`calling action : \${actionName}`);            

            switch(actionName) {
                default:
                    document.getElementById('cart-action').value = actionEl.dataset.action;
                    if( actionEl.dataset.index) {
                        document.getElementById('cart-index').value = actionEl.dataset.index;
                        console.log(`index : \${actionEl.dataset.index}`);
                    }
                    document.forms['{$formName}'].submit();
            }
        }
    });
    /**
     * Compoute and return the sum of all order values or -1 if one 
     */
    const computeOrderValueSum = () => Array.from(document.querySelectorAll('.order-value'))
        .reduce( (acc,cur) => {
            const num = Number(cur.value);
            if( cur.value.trim().length === 0 || isNaN(num) || acc == -1) {
                return -1;
            } else {
                return acc + num;
            }
        }, 0);

    const renderOrderValueSum = () => {
        const sum = computeOrderValueSum();
        const orderValueEl = document.getElementById('order-value-sum');
        if(orderValueEl) {
            orderValueEl.textContent = sum == -1 ? '????' : sum.toFixed(2);            
        }
    };

    const computeOrderDiscountPercent = (productValue, orderValue) => {
        const discount = orderValue - productValue;
        return (( 100 * discount ) / productValue).toFixed(0);
    };

    const renderOrderDiscount = (inputValue) => {
        const orderValue = inputValue.value;
        const index = inputValue.id.split('-')[1];

        const orderDiscountEl =document.getElementById(`order-discount-\${index}`);
        if( orderValue.trim().length === 0 || isNaN(orderValue) ) {
            // hide discount item
            orderDiscountEl.value = "";
        } else {
            const productValue = document.getElementById(`order-\${index}-product_id`).selectedOptions[0].dataset.value;
            const pcDiscount = computeOrderDiscountPercent(productValue,orderValue );
            if(pcDiscount == 0) {
                orderDiscountEl.value = "";
            } else {
                orderDiscountEl.value = pcDiscount;
            }
        } 
    };

    $('.order-value').on('change input', (ev) => {
        renderOrderValueSum();
        
        // render discount
        renderOrderDiscount(ev.target);
    });

    /**
     * Copy the selected option data-value to the text content of another element
     * 
     * Each option of the select element must own a "data-value" attribute whose value
     * is copied to the target element
     * 
     * @param sel the select element. It must own a "data-target-id" attribute with the value
     * of the element to update
     */
    const copySelectedProductValue = (sel) => {
        const targetElement = document.getElementById(sel.dataset.targetId);
        const productValue = sel.selectedOptions[0].dataset.value;
        targetElement.value = isNaN(productValue) ? '' : productValue;
    };

    const copyProductValueToOrderValue = (index) => {
        const productValue = document.getElementById(`order-\${index}-product_id`).selectedOptions[0].dataset.value;
        const orderValueInput = document.getElementById(`order-\${index}-value`);
        orderValueInput.value = isNaN(productValue) ? '' : productValue;
    };

    /**
     * Each time user selects a product :
     * - update product value display
     * - clear order value
     */
    $('.orders select[data-product]').change( (ev) => {
        // get line index
        const index = ev.target.id.split('-')[1];

        // copy product value to order value
        copyProductValueToOrderValue(index);
        copySelectedProductValue(ev.target);

        // update order value sum
        renderOrderValueSum();

        // clear order value discount
        renderOrderDiscount(document.getElementById(`order-\${index}-value`));        
    });


    $(document).ready( () => {
        document.querySelectorAll('.orders select[data-product').forEach( copySelectedProductValue );
        document.querySelectorAll('input.order-value').forEach( renderOrderDiscount );

        renderOrderValueSum();
    });
EOS;

$this->registerJs($jsScript, View::POS_READY, 'cart-manager');

?>
<div id="cart-manager-container" class="cart-check-out">
    <h1>cart Manager</h1>

    <?php $form = ActiveForm::begin(['options' => [ 'name' => $formName]]); ?>
        <?= Html::hiddenInput('action', '', [ 'id' => 'cart-action']) ?>
        <?= Html::hiddenInput('index', '', ['id' => 'cart-index']) ?>

        <h2>Orders</h2>
        <hr>
        <?= Html::button('add order', ['class' => 'btn btn-default', 'data-action' => 'add-order']) ?>

        <?php if (count($orders)): ?>
            <table class="table table-condensed table-hover orders">
                <thead>
                    <tr>
                        <th>Fournisseur</th>
                        <th>Beneficiaire</th>
                        <th>Product</th>
                        <th>Prix unitaire</th>
                        <th>discount (%)</th>
                        <th>Value</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php  foreach ($orders as $index => $order): ?>
                        <tr>
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
                                <?= $form->field($order, "[$index]product_id")
                                    ->listBox($products, [
                                        'size'=>1,
                                        'options' =>  $productOptions,
                                        'prompt' => 'select ...',
                                        'data-product' => true,
                                        'data-order-value-id' => Html::getInputId($order, "[$index]value"),
                                        'data-target-id' => "product-value-$index"
                                    ])
                                    ->label(false)
                                ?>
                            </td>
                            <td>
                                <div class="form-group" style="width:6em">
                                    <input type="text" 
                                        id="product-value-<?=$index?>" 
                                        class="form-control" 
                                        disabled="disabled">
                                </div>                            
                            </td>
                            <td>
                            <div class="form-group" style="width:6em;">
                                    <input type="text" 
                                        id="order-discount-<?=$index?>" 
                                        class="form-control" 
                                        disabled="disabled">
                                </div>                            
                            </td>
                            <td>
                                <?= $form->field($order, "[$index]value")
                                    ->textInput(['class' => 'order-value form-control', 'maxlength' => true, 'autocomplete'=> 'off'])
                                    ->label(false)
                                ?>
                            </td>
                            <td>
                                <?= Html::button(
                                    '<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>',
                                    ['class' => 'btn btn-danger btn-sm', 'data-action' => 'remove-order', 'data-index' => $index,
                                    'title' => 'remove']
                                ) ?>
                            </td>
                        </tr>        
                    <?php endforeach; ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <h3><span id="order-value-sum"></span></h3>
                            </td>
                            <td></td>
                        </tr>                    
                </tbody>
            </table>
        <?php endif; ?>

        <h2>Transactions</h2>
        <hr>
        <?= Html::button('add transaction', ['class' => 'btn btn-default', 'data-action' => 'add-transaction']) ?>

        <?php if (count($transactions)): ?>
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Value</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Code</th>
                        <th></th>
                    </tr>
                </thead>        
                <tbody>
                    <?php  foreach ($transactions as $index => $transaction): ?>
                        <tr>
                            <td>
                                <?= $form->field($transaction, "[$index]from_account_id")
                                    ->listBox($bankAccounts, ['size'=>1])
                                    ->label(false)
                                ?>
                            </td>
                            <td>
                                <?= $form->field($transaction, "[$index]to_account_id")
                                    ->listBox($bankAccounts, ['size'=>1])
                                    ->label(false)
                                ?>
                            </td>
                            <td>
                                <?= $form->field($transaction, "[$index]value")
                                    ->textInput(['maxlength' => true, 'autocomplete'=>'off'])
                                    ->label(false)
                                ?>
                            </td>
                            <td>
                                <?= $form->field($transaction, "[$index]reference_date")
                                    ->textInput(['maxlength' => true, 'autocomplete'=>'off'])
                                    ->label(false)
                                ?>
                            </td>
                            <td>
                                <?= $form->field($transaction, "[$index]type")
                                    ->listBox(\app\components\Constant::getTransactionTypes(), ['size'=>1])
                                    ->label(false)
                                ?>
                            </td>
                            <td>
                                <?= $form->field($transaction, "[$index]code")
                                    ->textInput(['maxlength' => true, 'autocomplete'=>'off'])
                                    ->label(false)
                                ?>
                            </td>
                            <td>
                                <?= Html::button(
                                    '<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>',
                                    ['class' => 'btn btn-danger btn-sm', 'data-action' => 'remove-transaction', 'data-index' => $index,
                                    'title' => 'remove']
                                ) ?>                        
                            </td>
                        </tr>        
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (count($orders) != 0 && count($transactions) != 0): ?>
            <?= Html::button('Submit Cart', ['class' => 'btn btn-primary', 'data-action' => 'submit']) ?>
        <?php endif;?>
    <?php ActiveForm::end(); ?>

</div>

