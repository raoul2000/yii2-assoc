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


    // order handlers /////////////////////////////////////////////////////


    /**
     * Compoute and return the sum of all order values or -1 if one 
     */

    const computeInputValueSum = (selector) => Array.from(document.querySelectorAll(selector))
        .reduce( (acc,cur) => {
            const num = Number(cur.value);
            if( cur.value.trim().length === 0 || isNaN(num) || acc == -1) {
                return -1;
            } else {
                return acc + num;
            }
        }, 0);

    const computeOrderValueSum = () => computeInputValueSum('.order-value');
    const computeTransactionValueSum = () => computeInputValueSum('.transaction-value');
    
    const renderValueSum = (selector, sumValue) => {
        const orderValueEl = document.getElementById(selector);
        if(orderValueEl) {
            orderValueEl.textContent = sumValue == -1 ? '????' : sumValue.toFixed(2);       
            orderValueEl.dataset.sumValue = sumValue;         
        }
    };
    const renderOrderValueSum = () => renderValueSum('order-value-sum', computeOrderValueSum());
    const renderTransactionValueSum = () => renderValueSum('transaction-value-sum', computeTransactionValueSum());

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
        renderOrderDiscount(ev.target);
        renderOrderValueSum();
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


    // transaction handlers /////////////////////////////////////////////////////

    $('#btn-report-sum-order').on('click', (ev) => {
        const transactionInputs = document.querySelectorAll('#transactions input.transaction-value');
        if(transactionInputs.length == 0) {
            return; // no transaction to report to
        }

        /*
        const totalTransactionValue = Array.from(document.querySelectorAll('#transactions input.transaction-value'))
            .reduce((acc, curr) => acc+ Number(curr.value), 0 );
        */
        const sumValue = document.getElementById('order-value-sum').dataset.sumValue;
        if(sumValue != -1) {
            const valueToReport = ( Number(sumValue) / transactionInputs.length).toFixed(2);
            transactionInputs.forEach( (el) => {
                el.value = valueToReport;
            });
            renderTransactionValueSum();
        }
    });

    $('.transaction-value').on('change input', (ev) => {
        renderTransactionValueSum();
    });

    /////////////////////////////////////////////////////////////////////////////
    $(document).ready( () => {
        document.querySelectorAll('.orders select[data-product').forEach( copySelectedProductValue );
        document.querySelectorAll('input.order-value').forEach( renderOrderDiscount );

        renderOrderValueSum();
        renderTransactionValueSum();
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
        <?= Html::button('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> add order', ['class' => 'btn btn-success', 'data-action' => 'add-order']) ?>

        <?php if (count($orders)): ?>
            <table id="orders" class="table table-condensed table-hover orders">
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
        <?= Html::button('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>  add transaction', ['class' => 'btn btn-success', 'data-action' => 'add-transaction']) ?>
        <?= Html::button('report total order value', ['id' => 'btn-report-sum-order','class' => 'btn btn-default']) ?>

        <?php if (count($transactions)): ?>
            <table id="transactions" class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Code</th>
                        <th>Value</th>
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
                                <?= $form->field($transaction, "[$index]value")
                                    ->textInput(['class' => 'transaction-value form-control', 'maxlength' => true, 'autocomplete'=>'off'])
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
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <h3><span id="transaction-value-sum"></span></h3>
                        </td>
                        <td></td>
                    </tr>                     
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (count($orders) != 0 && count($transactions) != 0): ?>
            <?= Html::button('Submit Cart', ['class' => 'btn btn-primary', 'data-action' => 'submit']) ?>
        <?php endif;?>
    <?php ActiveForm::end(); ?>

</div>

