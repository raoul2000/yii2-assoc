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

        <table id="orders" class="table table-condensed table-hover orders">
            <thead>
                <tr>
                    <th><?= \Yii::t('app', 'Product') ?></th>
                    <th><?= \Yii::t('app', 'Prix unitaire') ?></th>
                    <th><?= \Yii::t('app', 'Value') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders_1 as $index => $order):?>
                    <tr>
                        <td>
                            <?= Html::encode($products_1[$order->product_id]->name) ?>
                        </td>
                        <td>
                            <input type="text" 
                                value="<?= $products_1[$order->product_id]->value ?>" 
                                class="form-control" 
                                title="unitary value"
                                disabled="disabled">
                        </td>
                        <td>
                            <?= $form->field($order, "[$index]value")
                                ->textInput(['class' => 'order-value form-control', 'maxlength' => true, 'autocomplete'=> 'off'])
                                ->label(false)
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php foreach ($orders_2 as $index => $order):?>
                    <tr class="success">
                        <td>
                            <?= Html::encode($products_2[$order->product_id]->name) ?>
                        </td>
                        <td>
                            <input type="text" 
                                value="<?= $products_2[$order->product_id]->value ?>" 
                                class="form-control" 
                                title="unitary value"
                                disabled="disabled">
                        </td>
                        <td>
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
                        <td class="valueSum">
                            <h3><span id="order-value-sum">???</span></h3>
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