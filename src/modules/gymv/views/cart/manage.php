<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */

$formName = 'cart-manager-form'; // WARNING : JS code below refers to formName value
$countTransactions = count($transactions);
$countOrders = count($orders);

$this->registerJs(file_get_contents(__DIR__ . '/manage.js'), View::POS_READY, 'cart-manager');

?>
<style>
    #cart-manager-container .table > thead > tr > th, 
    #cart-manager-container .table > tbody > tr > th, 
    #cart-manager-container .table > thead > tr > td, 
    #cart-manager-container .table > tbody > tr > td {
        border-top-width: 0px;
    }

    #cart-manager-container .table > tbody > tr > td.valueSum {
        border-top: 2px solid #ddd;
        text-align:right;
    }
</style>
<div id="cart-manager-container">

    <?php 
        // define the modal to "save template As ..."
        yii\bootstrap\Modal::begin([
            'id' => 'save-template-modal',
            'header' => '<h3>Save Template As ...</h3>',
            'footer' => '
            <div id="btnbar-start">
                <button id="btn-save-as-template" class="btn btn-primary">Save</button>&nbsp;<button class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
            <div id="btnbar-end">
                <button class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
                ',
            'closeButton' => false, // no close button
            'clientOptions' => [
                'keyboard' => false // no close on ESC
            ]
        ]); ?>

        <div class="alert alert-danger" role="alert">
            An error occured that prevent the template for being saved.
        </div>
        <div class="alert alert-success" role="alert">
            Template saved correctly.
        </div>
        <div class="alert alert-info" role="alert">
            Saving Template ...
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="template-name" placeholder="enter template name ....">
        </div>

    <?php
        yii\bootstrap\Modal::end();
    ?>
    <h1>cart Manager</h1>

    <?php $form = ActiveForm::begin(['options' => [ 'id' => 'cart-manager-form', 'name' => $formName]]); ?>
        <?= Html::hiddenInput('action', '', [ 'id' => 'cart-action']) ?>
        <?= Html::hiddenInput('index', '', ['id' => 'cart-index']) ?>
        <?= Html::hiddenInput('template-name', '', ['id' => 'cart-template-name']) ?>

        <h2>Orders</h2>
        <hr>
        <?= Html::button('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> add order', ['class' => 'btn btn-success', 'data-action' => 'add-order']) ?>

        <?php if ($countOrders): ?>
            <table id="orders" class="table table-condensed table-hover orders">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Fournisseur</th>
                        <th>Beneficiaire</th>
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
                                <?= $form->field($order, "[$index]product_id")
                                    ->listBox($products, [
                                        'size'=>1,
                                        'options' =>  $productOptions,
                                        'prompt' => 'select ...',
                                        'data-product' => true,
                                        'data-order-value-id' => Html::getInputId($order, "[$index]value"),
                                        'data-target-id' => "product-value-$index",
                                        'style' => 'font-weight:bold;'
                                    ])
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
                                        class="form-control order-discount" 
                                        autocomplete="off">
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
                        <td class="valueSum">
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
        <?php if ($countOrders != 0 && $countTransactions != 0): ?>
            <?= Html::button('report total order value', ['id' => 'btn-report-sum-order','class' => 'btn btn-default']) ?>
        <?php endif; ?>

        <?php if ($countTransactions): ?>
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
                        <td class="valueSum">
                            <h3><span id="transaction-value-sum"></span></h3>
                        </td>
                        <td></td>
                    </tr>                     
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($countOrders != 0 && $countTransactions != 0): ?>
            <?= Html::button('Submit Cart', ['class' => 'btn btn-primary', 'data-action' => 'submit']) ?>
            <?= Html::button(
                'Save As Template ....',
                [
                    'class' => 'btn btn-default',
                    'data-toggle' => 'modal',
                    'data-target' => '#save-template-modal'
                ]
            ) ?>
        <?php endif;?>
    <?php ActiveForm::end(); ?>
</div>

