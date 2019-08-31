<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
//$this->registerJs(file_get_contents(__DIR__ . '/address.js'), View::POS_READY, 'registration-address');
?>
<div>
    <style>
        #productform-top_products label {
            display:block;
        }
        .product-result {
            margin:4px;
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
                <?= $form->field($model, 'top_products')
                    ->checkboxList(\app\modules\gymv\models\ProductForm::getTopProductsList())?>
            </div>
            <div class="col-xs-7">
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
                                        this.productItemsData = [];
                                    }
                                    this.productItemsData.push(item);
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
                                console.log(value);
                                //this.clearOptions();
                                this.clear(true);
                                //console.log(this.getItem(value));
                                console.log(this.productItemsData);
                                //this.addOption({ id : 333, name : 'hello'});
                                //$('#selectized-product').selectize.addOption({ id : 333, name : 'hello'});                                
                            }
                        ")
                    ],
                ]); ?>        
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