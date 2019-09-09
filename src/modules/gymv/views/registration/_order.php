<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
//$this->registerJs(file_get_contents(__DIR__ . '/address.js'), View::POS_READY, 'registration-address');
?>
<div id="wiz-order">
    <h3>
        <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Orders') ?>
    </h3>

    <hr/>

    <?php $form = ActiveForm::begin(); ?>
        <?php 
            // echo $form->errorSummary($orderModels);
         ?>
        <table id="orders" class="table table-condensed table-hover orders">
            <thead>
                <tr>
                    <th><?= \Yii::t('app', 'Product') ?></th>
                    <th><?= \Yii::t('app', 'From') ?></th>
                    <th><?= \Yii::t('app', 'Until') ?></th>
                    <th><?= \Yii::t('app', 'Prix unitaire') ?></th>
                    <th><?= \Yii::t('app', 'Value') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderModels as $index => $order):?>
                    <tr>
                        <td width="50%">
                            <?= Html::encode($products[$order->product_id]['name']) ?>
                        </td>
                        <td>
                            <?= $form->field($order, "[$index]valid_date_start")
                                ->textInput(['class' => 'order-valid_date_start form-control', 'maxlength' => true, 'autocomplete'=> 'off'])
                                ->label(false)
                            ?>
                        </td>
                        <td>
                            <?= $form->field($order, "[$index]valid_date_end")
                                ->textInput(['class' => 'order-valid_date_end form-control', 'maxlength' => true, 'autocomplete'=> 'off'])
                                ->label(false)
                            ?>
                        </td>

                        <td width="10%">
                            <input type="text" 
                                value="<?= $products[$order->product_id]['value'] ?>" 
                                class="form-control" 
                                title="unitary value"
                                disabled="disabled">
                        </td>
                        <td width="10%">
                            <?= $form->field($order, "[$index]value")
                                ->textInput(['class' => 'order-value form-control', 'maxlength' => true, 'autocomplete'=> 'off'])
                                ->label(false)
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="valueSum">
                        <h3><span id="order-value-sum"><?= $orderTotalValue ?></span></h3>
                    </td>
                    <td></td>
                </tr>                    
            </tbody>
        </table>


        <hr/>

        <div class="form-group">
            <?= Html::a(
                '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> ' . \Yii::t('app', 'Previous'),
                ['product-select'],
                ['class' => 'btn btn-primary']
            )?>
            <?= Html::submitButton(
                \Yii::t('app', 'Next') . ' <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>',
                ['class' => 'btn btn-primary']
            )?>
        </div>
    <?php ActiveForm::end(); ?>
</div>