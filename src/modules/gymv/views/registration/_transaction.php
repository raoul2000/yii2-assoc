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
    </h3>

    <hr/>
    <p>
        Total : <?= $orderTotalValue ?>
    </p>
    <?= Html::button(
            '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' . \Yii::t('app', 'add transaction'), 
            ['class' => 'btn btn-success', 'data-action' => 'add-transaction']
        ) ?>

    <?php $form = ActiveForm::begin(['options' => [ 'id' => 'transaction-manager-form', 'name' => 'tr-form']]); ?>

        <?= Html::hiddenInput('action', '', [ 'id' => 'tr-action']) ?>
        <?= Html::hiddenInput('index', '', ['id' => 'tr-index']) ?>

        <?php 
            echo $form->errorSummary($transactionModels);
        ?>

        <table id="transactions" class="table table-condensed table-hover transactions">
            <tbody>
                <?php foreach ($transactionModels as $index => $transaction):?>
                    <tr>
                        <td>
                            <?= $index+1 ?>
                        </td>
                        <td>
                            <?= $form->field($transaction, "[$index]reference_date")
                                ->textInput(['class' => 'transaction-reference_date form-control', 'maxlength' => true, 'autocomplete'=> 'off'])
                                
                            ?>
                        </td>
                        <td>
                            <?= $form->field($transaction, "[$index]value")
                                ->textInput(['class' => 'transaction-value form-control', 'maxlength' => true, 'autocomplete'=> 'off'])
                                
                            ?>
                        </td>
                        <td>
                            <?php if($index != 0) {
                                echo Html::button(
                                    '<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>',
                                    [
                                        'class' => 'btn btn-danger btn-sm', 
                                        'data-action' => 'remove-transaction', 
                                        'data-index' => $index,
                                        'title' => 'remove'
                                    ]
                                );
                            }?>
                        </td>
                    </tr>
                <?php endforeach; ?>
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
                \Yii::t('app', 'Next') . ' <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>',
                ['class' => 'btn btn-primary']
            )?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
