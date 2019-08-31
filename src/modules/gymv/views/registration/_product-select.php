<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$this->registerJs(file_get_contents(__DIR__ . '/_product-select.js'), View::POS_READY, 'registration-product-select');
?>
<div>
    <style>
        #productform-top_products label {
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
    <h3>Product Select</h3>
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
                <h3>Top Product</h3>
                <?= $form->field($model, 'top_products')
                    ->checkboxList(\app\modules\gymv\models\ProductForm::getTopProductsList())
                    ->label(false)
                ?>
            </div>
            <div class="col-xs-7">
                <h3>Product</h3>
                <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                    'name' => 'productId',
                    'id' => 'selectized-product',
                    'loadUrl' => ['ajax-select-product'],
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
                                            + '<span class=\"glyphicon glyphicon-gift\" aria-hidden=\"true\"></span>' 
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