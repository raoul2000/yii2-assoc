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
        // modal BEGIN -------------------------------------------------------------
        // Form settings
        yii\bootstrap\Modal::begin([
            'id' => 'form-settings-modal',
            'header' => '<h3>' . \Yii::t('app', 'Settings') . '</h3>',
            'footer' => '
                <div id="btnbar-start">
                    <button id="btn-save-form-settings" class="btn btn-primary">'
                        . \Yii::t('app', 'Save') . 
                    '</button>
                    &nbsp;
                    <button class="btn btn-default" data-dismiss="modal">' . \Yii::t('app', 'Close') . '</button>
                </div>
            '
        ]); ?>

        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input id="order-lock-provider" type="checkbox"> <?= \Yii::t('app', 'lock order Provider') ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input id="order-lock-beneficiary" type="checkbox"> <?= \Yii::t('app', 'lock order Beneficiary') ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input id="order-lock-start-date" type="checkbox"> <?= \Yii::t('app', 'lock order Valid Start Date') ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input id="order-lock-end-date" type="checkbox"> <?= \Yii::t('app', 'lock order Valid End Date') ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input id="order-enable-report" type="checkbox"> <?= \Yii::t('app', 'Enable auto report order Sum to transaction') ?>
                </label>
            </div>
        </div>
    <?php
        yii\bootstrap\Modal::end();
         // modal END -------------------------------------------------------------
    ?>    




    <?php 
        // modal BEGIN -------------------------------------------------------------
        // define the modal to "save template As ..."
        yii\bootstrap\Modal::begin([
            'id' => 'save-template-modal',
            'header' => '<h3>' . \Yii::t('app', 'Save Template') . '</h3>',
            'footer' => '
            <div id="tmpl-btnbar-start">
                <button id="btn-save-as-template" class="btn btn-primary">' . \Yii::t('app', 'Save') . '</button>&nbsp;
                <button class="btn btn-default" data-dismiss="modal">' . \Yii::t('app', 'Cancel') . '</button>
            </div>
            <div id="tmpl-btnbar-end">
                <button class="btn btn-primary" data-dismiss="modal">' . \Yii::t('app', 'Close') . '</button>
            </div>
                ',
            'closeButton' => false, // no close button
            'clientOptions' => [
                'keyboard' => false // no close on ESC
            ]
        ]); ?>

        <div class="alert alert-danger" role="alert">
            <?= \Yii::t('app', 'An error occured that prevent the template for being saved.') ?>
        </div>
        <div class="alert alert-success" role="alert">
           <?  \Yii::t('app', 'Template saved correctly.') ?>
        </div>
        <div class="alert alert-info" role="alert">
            <? \Yii::t('app', 'Saving Template ...') ?>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="template-name" placeholder="<?= \Yii::t('app', 'enter template name ....') ?>">
        </div>
    <?php
        yii\bootstrap\Modal::end();
         // modal END -------------------------------------------------------------
    ?>    




    <div class="toolbar">
        <div class="btn-group">     
            <?= Html::button(
                \Yii::t('app', 'Template...'),
                [
                    'class' => 'btn btn-default',
                    'data-toggle' => 'dropdown'
                ]
            )?>                                         

            <ul class="dropdown-menu">
                <?php if( !empty($templateName) ) :?>
                    <li class="dropdown-header">
                        <?= \Yii::t('app', 'Template') ?> : <?= Html::encode($templateName) ?>
                    </li>
                    <li role="separator" class="divider"></li>
                <?php endif; ?>
                <li>
                    <?= Html::a(
                        \Yii::t('app', 'Select Template'),
                        ['select-template']
                    )?>                 
                </li>
                <?php if ($countOrders != 0 || $countTransactions != 0): ?>
                    <li>
                    <?= Html::a(
                        \Yii::t('app', 'Save As ...'),
                        '#',
                        [
                            'data-toggle' => 'modal',
                            'data-target' => '#save-template-modal'
                        ]
                    ) ?>
                    </li>
                <?php endif;?>                
            </ul>
        </div>      

        <?php if ($countOrders != 0 || $countTransactions != 0): ?>
            <?= Html::button(\Yii::t('app', 'Reset'), ['class' => 'btn btn-danger', 'data-action' => 'reset']) ?>
        <?php endif;?>      

        <?= Html::button(
            \Yii::t('app', 'Settings'), 
            [
                'id' => 'btn-open-settings-modal', 
                'class' => 'btn btn-default',
                'data-toggle' => 'modal',
                'data-target' => '#form-settings-modal'
            ]
        )?>
    </div>

    <?php $form = ActiveForm::begin(['options' => [ 'id' => 'cart-manager-form', 'name' => $formName]]); ?>
        <?= Html::hiddenInput('action', '', [ 'id' => 'cart-action']) ?>
        <?= Html::hiddenInput('index', '', ['id' => 'cart-index']) ?>
        <?= Html::hiddenInput('template-name', '', ['id' => 'cart-template-name']) ?>

        <h2>      
            <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
            <?= \Yii::t('app', 'Orders') ?>
        </h2>
        <hr>
        <?= Html::button(
            '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' . \Yii::t('app', 'add order'), 
            ['class' => 'btn btn-success', 'data-action' => 'add-order']
        )?>

        <?php if ($countOrders): ?>
            <table id="orders" class="table table-condensed table-hover orders">
                <thead>
                    <tr>
                        <th><?= \Yii::t('app', 'Product') ?></th>
                        <th><?= \Yii::t('app', 'Fournisseur') ?></th>
                        <th><?= \Yii::t('app', 'Beneficiaire') ?></th>
                        <th><?= \Yii::t('app', 'From') ?></th>
                        <th><?= \Yii::t('app', 'Until') ?></th>
                        <th><?= \Yii::t('app', 'Prix unitaire') ?></th>
                        <th><?= \Yii::t('app', 'discount') ?> (%)</th>
                        <th><?= \Yii::t('app', 'Value') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php  foreach ($orders as $index => $order): ?>
                        <tr>
                            <td style="min-width:15em">
                                <?= $form->field($order, "[$index]product_id")
                                    ->listBox($products, [
                                        'size'=>1,
                                        'options' =>  $productOptions,
                                        'prompt' => \Yii::t('app', 'select ...'),
                                        'data-product' => true,
                                        'data-order-value-id' => Html::getInputId($order, "[$index]value"),
                                        'data-target-id' => 'product-value-' . $index,
                                        'style' => 'font-weight:bold;'
                                    ])
                                    ->label(false)
                                ?>
                            </td>                        
                            <td style="min-width:15em">
                                <?= $form->field($order, "[$index]from_contact_id")
                                    ->listBox($contacts, [
                                        'size'=>1,
                                        'prompt' => \Yii::t('app', 'select ...'),
                                        'data-from-contact-id' => true,
                                        'data-sync-setting'    => 'orderLockProvider',
                                        'data-sync-selector'   => '.orders select[data-from-contact-id]'
                                    ])
                                    ->label(false)
                                ?>
                            </td>
                            <td style="min-width:15em">
                                <?= $form->field($order, "[$index]to_contact_id")
                                    ->listBox($contacts, [
                                        'size'=>1,
                                        'prompt' => \Yii::t('app', 'select ...'),
                                        'data-to-contact-id' => true,
                                        'data-sync-setting'  => 'orderLockBeneficiary',
                                        'data-sync-selector' => '.orders select[data-to-contact-id]'

                                    ])
                                    ->label(false)
                                ?>
                            </td>    
                            <td style="min-width:8em">
                                <?= $form->field($order, "[$index]valid_date_start")
                                    ->textInput([
                                        'class' => 'form-control',
                                        'maxlength' => true,
                                        'autocomplete'=> 'off',
                                        'data-date-start'    => true,
                                        'data-sync-setting'  => 'orderLockStartDate',
                                        'data-sync-selector' => '.orders input[data-date-start]',
                                        'title' => \Yii::t('app', 'ex: 30/01/2019')
                                    ])
                                    ->label(false)
                                ?>
                            </td>                                                
                            <td style="min-width:8em">
                                <?= $form->field($order, "[$index]valid_date_end")
                                    ->textInput([
                                        'class' => 'form-control',
                                        'maxlength' => true,
                                        'autocomplete'=> 'off',
                                        'data-date-end'      => true,                           // used to select this input (see "data-sync-selector" )
                                        'data-sync-setting'  => 'orderLockEndDate',             // name of the form settings property for lock end date
                                        'data-sync-selector' => '.orders input[data-date-end]', // selector for all end date inputs
                                        'title' => \Yii::t('app', 'ex: 31/12/2020')
                                    ])
                                    ->label(false)
                                ?>
                            </td>                                                
                            <td style="min-width:2em">
                                <div class="form-group">
                                    <input type="text" 
                                        id="product-value-<?=$index?>" 
                                        class="form-control" 
                                        disabled="disabled">
                                </div>                            
                            </td>
                            <td  style="min-width:2em">
                                <div class="form-group">
                                    <input type="text" 
                                        id="order-discount-<?=$index?>" 
                                        class="form-control order-discount" 
                                        autocomplete="off">
                                </div>                            
                            </td>
                            <td  style="min-width:3em">
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

        <h2>
            <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> 
            <?= \Yii::t('app', 'Transactions') ?>
        </h2>
        <hr>
        <?= Html::button(
            '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' . \Yii::t('app', 'add transaction'), 
            ['class' => 'btn btn-success', 'data-action' => 'add-transaction']
        ) ?>
        <?php if ($countOrders != 0 && $countTransactions != 0): ?>
            <?= Html::button(\Yii::t('app', 'report total order value'), ['id' => 'btn-report-sum-order','class' => 'btn btn-default']) ?>
        <?php endif; ?>

        <?php if ($countTransactions): ?>
            <table id="transactions" class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th><?= \Yii::t('app', 'From') ?></th>
                        <th><?= \Yii::t('app', 'To') ?></th>
                        <th><?= \Yii::t('app', 'Date') ?></th>
                        <th><?= \Yii::t('app', 'Type') ?></th>
                        <th><?= \Yii::t('app', 'Code') ?></th>
                        <th><?= \Yii::t('app', 'Value') ?></th>
                        <th></th>
                    </tr>
                </thead>        
                <tbody>
                    <?php  foreach ($transactions as $index => $transaction): ?>
                        <tr>
                            <td style="min-width:15em">
                                <?= $form->field($transaction, "[$index]from_account_id")
                                    ->listBox($bankAccounts, [
                                        'size'=>1,
                                        'prompt' => \Yii::t('app', 'select ...'),
                                        'data-from-account-id' => true,
                                    ])
                                    ->label(false)
                                ?>
                            </td>
                            <td style="min-width:15em">
                                <?= $form->field($transaction, "[$index]to_account_id")
                                    ->listBox($bankAccounts, [
                                        'size'=>1,
                                        'prompt' => \Yii::t('app', 'select ...'),
                                        'data-to-account-id' => true,
                                    ])
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
            <?= Html::button(\Yii::t('app', 'Submit Cart'), ['class' => 'btn btn-primary', 'data-action' => 'submit']) ?>
        <?php endif; ?>
    <?php ActiveForm::end(); ?>
</div>

