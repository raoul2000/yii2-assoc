<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$this->registerJs(file_get_contents(__DIR__ . '/_transaction.js'), View::POS_READY, 'registration-transaction');
?>
<div id="wiz-transaction">
    <h3>
        <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Transaction') ?>
        <small class="wizard-step"><?= \Yii::t('app', 'step') ?> 5/5</small>
    </h3>

    <hr/>
    <div class="alert alert-info">
        <b><?= \Yii::t('app', 'Total To Pay') ?> : <?= $orderTotalValue ?></b><br/>
        <?= \Yii::t('app', 'You can use one, two or more transaction to pay this amount. Make sure that total match.') ?>
    </div>

    <?php $form = ActiveForm::begin(['options' => [ 'id' => 'transaction-manager-form', 'name' => 'tr-form']]); ?>

        <?= Html::hiddenInput('action', '', [ 'id' => 'tr-action']) ?>
        <?= Html::hiddenInput('index', '', ['id' => 'tr-index']) ?>

        <?php 
            echo $form->errorSummary($transactionModels);
        ?>

        <?php if (count($fromAccounts) > 1): ?>
            <div class="alert alert-info">
                <p>
                    The contact <b><?= html::encode($contact->longName) ?></b> has more than one bank account. Select the one you want to use :
                </p>
                <div class="form-group">
                    <?= Html::dropDownList('fromAccountId', null, $fromAccounts, [
                        'id' => 'template-list',
                        'size'=>1,
                        'prompt' => \Yii::t('app', 'select the account ...'),
                        'class' => 'form-control'
                    ])?>
                </div>            
            </div>
        <?php endif; ?>

        <div class="toolbar" style="margin-bottom:1em;">
            <?= Html::button(
                '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' . \Yii::t('app', 'add transaction'), 
                ['class' => 'btn btn-warning', 'data-action' => 'add-transaction']
            ) ?>
        </div>

        <table id="transactions" class="table table-condensed table-hover transactions">
            <tbody>
                <?php foreach ($transactionModels as $index => $transaction):?>
                    <tr>
                        <td style="font-size: 2em;vertical-align: middle;">
                            <?= $index+1 ?>
                        </td>
                        <td>
                            <?= $form->field($transaction, "[$index]reference_date")
                                ->textInput([
                                    'class'         => 'transaction-reference_date form-control', 
                                    'maxlength'     => true, 
                                    'autocomplete'  => 'off',
                                    'placeholder'   => \Yii::t('app', 'Ex: 23/01/2001'),
                                ])
                            ?>
                        </td>
                        <td>
                            <?= $form->field($transaction, "[$index]code")
                                ->textInput([
                                    'class'         => 'transaction-code form-control', 
                                    'maxlength'     => true, 
                                    'autocomplete'  => 'off',
                                    'placeholder'   => \Yii::t('app', 'enter check number'),
                                ])
                            ?>
                        </td>
                        <td>
                            <?= $form->field($transaction, "[$index]value")
                                ->textInput([
                                    'class'         => 'transaction-value form-control', 
                                    'maxlength'     => true, 
                                    'autocomplete'  => 'off'
                                ])
                            ?>
                        </td>
                        <td style="vertical-align: middle;">
                            <?php if($index != 0) {
                                echo Html::button(
                                    '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    [
                                        'class'         => 'btn btn-default btn-sm remove-transaction-line', 
                                        'data-action'   => 'remove-transaction', 
                                        'data-index'    => $index,
                                        'title'         => \Yii::t('app', 'remove')
                                    ]
                                );
                            }?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td class="total-label">
                        Total
                    </td>
                    <td class="total-value">
                        <span id="diff-marker" class="no-match">
                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                        </span>
                        <span id="transaction-value-sum"></span>
                        <span class="expected-total" style="display:none"><?= $orderTotalValue ?></span>
                        <span id="expected-total-value" data-value="<?= $orderTotalValue ?>" />
                    </td>
                </tr>

            </tbody>
        </table>

        <hr/>

        <div class="form-group">
            <?= Html::a(
                '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> ' . \Yii::t('app', 'Previous'),
                ['order'],
                ['class' => 'btn btn-primary']
            )?>
            <?= Html::submitButton(
                '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> '
                    . \Yii::t('app', 'Finish and save') ,
                ['class' => 'btn btn-success']
            )?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
