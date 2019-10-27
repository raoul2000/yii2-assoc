<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;
use app\modules\gymv\models\ProductSelectionForm;

/* @var $this yii\web\View */
$this->registerJs(file_get_contents(__DIR__ . '/_product-select.js'), View::POS_READY, 'registration-product-select');
?>
<div id="wiz-product-select">
    <h3>
        <span class="glyphicon glyphicon-gift" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Product Selection') ?>
        <small class="wizard-step"><?= \Yii::t('app', 'step') ?> 3/5</small>
    </h3>

    <hr/>

    <?php $form = ActiveForm::begin(); ?>

        <?php if ($model->hasErrors()) {
            echo $form->errorSummary($model);
        }?>

        <div class="row">
            <div class="col-xs-5">
                <!--
                <?= $form->field($model, 'product_ids')
                    ->checkboxList($firstClassProductIndex)
                    ->label(false)
                ?>
                -->

                <?= $form->field($model, 'adhesion')
                    ->label('Adhésion Gymv : ')
                    ->radioList(['1' => 'Vincennois', '2' => 'non Vincennois'],
                [
                    'class' => 'radio',
                    'itemOptions' => [
                        'style' => 'display: block'
                    ]
                ])?> 
                
                <div class="form-group">
                    <label for="" class="control-label">License Fédération :</label>
                    <div class="radio">
                        <label>
                            <input id="radio-no-achat-licence" type="radio" name="ProductSelectionForm[achat_license]" value="1" style="display: block"> Déjà licencié
                        </label>
                        <label>
                            <input id="radio-achat-licence" type="radio" name="ProductSelectionForm[achat_license]" value="2" style="display: block"> Achat license
                        </label>
                        <div id="container-achat-licence" class="radio" style="display:none">
                            <label>
                                <input type="radio" name="ProductSelectionForm[license_type]" value="1" style="display: block"> License adulte
                            </label>
                            <label>
                                <input type="radio" name="ProductSelectionForm[license_type]" value="2" style="display: block"> license enfant
                            </label>
                            <div class="form-group field-productselectionform-fed_assurance">
                                <input type="hidden" name="ProductSelectionForm[fed_assurance]" value="0">
                                <label style="padding-left: 0;padding-top: 1em;">
                                    <input type="checkbox" id="productselectionform-fed_assurance" name="ProductSelectionForm[fed_assurance]" value="1"> Assurance optionnelle
                                </label>
                            </div>
                        </div> 
                    </div>
                </div>

                <?= $form->field($model, 'sorano')
                    ->checkbox([
                        'label' => "Inscription Espace Sorano"
                ])?>
            </div>

            <div class="col-xs-7">
                <?= \dosamigos\selectize\SelectizeDropDownList::widget([
                    'name' => 'productId',
                    'id' => 'selectized-product',
                    'loadUrl' => ['ajax-product-search'],
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
                                    const html ='<div class=\"product-result\">' 
                                        + '<span class=\"product-name\">'
                                            + '<span class=\"glyphicon glyphicon-gift\" aria-hidden=\"true\"></span> ' 
                                            + escape(item.name)
                                        + '</span>'
                                        + ( item.short_description 
                                            ? '<span class=\"product-short-description\">'
                                                + escape(item.short_description)
                                                + '</span>'
                                            : ''
                                        )
                                        + 
                                    + '</div>';
                                    return html;
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
                    <?php foreach ($model->getSelectedProductIdsByGroup(ProductSelectionForm::GROUP_2) as $productId) :?>
                        <div class="selected-product-item" data-item-id="<?= $productId ?>">
                            <div class="product-remove" title="remove">
                                <span data-action="remove" class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            </div>

                            <span class="product-name">
                                <span class="glyphicon glyphicon-gift" aria-hidden="true"></span> <?= Html::encode($products_2[$productId]['name']) ?>
                            </span>

                            <?php if (!empty($products_2[$productId]['short_description'])): ?>
                                <span class="product-short-description">
                                    <?= Html::encode($products_2[$productId]['short_description']) ?>
                                </span>
                            <?php endif; ?>

                            <input type="hidden" name="ProductSelectionForm[product_ids][]" value="<?= $productId ?>">
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