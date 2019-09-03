<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$this->registerJs(file_get_contents(__DIR__ . '/_product-select.js'), View::POS_READY, 'registration-product-select');
?>
<div>
    <style>
        #productform-products_1 label {
            display:block;
        }
        .product-result {
            margin:4px;
        }
        .selected-product-item {
            float:left;
            width: 100%;
            padding:0.5em;
        }
        .selected-product-item:hover {
            background-color : #eee;
        }
        .selected-product-item .product-remove{
            float:right;
            color: red;
            cursor:pointer;
            
        }

    </style>

    <h3>
        <span class="glyphicon glyphicon-home" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Product Selection') ?>
    </h3>

    <hr/>

    <?php $form = ActiveForm::begin([
        //'layout' => 'vertical',
        //'options' => ['class' => 'form-horizontal']
    ]); ?>

        <?php if ($model->hasErrors()) {
            echo $form->errorSummary($model);
        }?>
        <div class="row">
            <div class="col-xs-5">
                <?= $form->field($model, 'products_1')
                    ->checkboxList($firstClassProductIndex)
                    ->label(false)
                ?>
            </div>
            <div class="col-xs-7">
                <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                    'name' => 'productId',
                    'id' => 'selectized-product',
                    'loadUrl' => ['/api/product/search'], //['ajax-select-product'],
                    'queryParam' => 'name',
                    'options' => ['class' => 'form-control'],
                    'clientOptions' => [
                        'placeholder' => \Yii::t('app', 'enter a product name ...'),
                        'valueField' => 'id',
                        'labelField' => 'name',
                        'searchField' => 'name',                            
                        'render' => [
                            'option' => new \yii\web\JsExpression("
                                function(item, escape) {
                                    if(!this.productItemsData) {
                                        this.productItemsData = {};
                                    }
                                    this.productItemsData[item.id] = item;
                                    return '<div>' 
                                        + '<span class=\"product-result\">'
                                            + '<span class=\"glyphicon glyphicon-gift\" aria-hidden=\"true\"></span> ' 
                                                + escape(item.name)
                                            + '</span>'
                                        + '</span>' 
                                    + '</div>';
                                }
                            ")
                        ],
                        'onChange' => new \yii\web\JsExpression("
                            function(value) {
                                this.clear(true);
                                this.clearOptions();
                                gymv.addToSelectedProducts(this.productItemsData[value]);
                                this.focus();
                            }
                        ")
                    ],
                ]); ?>   
                <div id="selected-product-list">
                    <?php foreach ($model->products_2 as $productId) :?>
                        <div class="selected-product-item" data-item-id="<?= $productId ?>">
                            <div class="product-remove" title="remove">
                                <span data-action="remove" class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            </div>

                            <span class="product-name">
                                <span class="glyphicon glyphicon-gift" aria-hidden="true"></span> <?= Html::encode($products_2[$productId]['name']) ?>
                            </span>
                            <input type="hidden" name="ProductForm[products_2][]" value="3">
                        </div>
                    <?php endforeach ?>
                </div>     
            </div>
        </div>
        <hr/>
        <div class="form-group">
            <?= Html::a(
                '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> ' . \Yii::t('app', 'Previous'),
                ['address-edit'],
                ['class' => 'btn btn-primary']
            )?>

            <?= Html::submitButton(
                \Yii::t('app', 'Next') . ' <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>',
                ['class' => 'btn btn-primary']
            )?>
        </div>

    <?php ActiveForm::end(); ?>

</div>