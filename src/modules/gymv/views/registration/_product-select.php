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

                <?= $form->field($model, 'adhesion')
                    ->label('Adhésion Gymv : ')
                    ->radioList([
                        ProductSelectionForm::ADHESION_VINCENNOIS => 'Vincennois', 
                        ProductSelectionForm::ADHESION_NON_VINCENNOIS => 'non Vincennois'],
                        [
                            'class' => 'radio',
                            'itemOptions' => [
                                'style' => 'display: block'
                            ]
                        ]
                    )
                ?> 

                <?= $form->field($model, 'achat_licence')
                    ->label('Licence Fédération :')
                    ->dropDownList(
                        [
                            ProductSelectionForm::DEJA_LICENCIE        => 'déjà licencié', 
                            ProductSelectionForm::ACHAT_LICENCE_ADULTE => 'licence ADULTE',
                            ProductSelectionForm::ACHAT_LICENCE_ENFANT => 'licence ENFANT',
                        ],[
                            'id'        => 'achat_licence',
                            'options'   => [
                                ProductSelectionForm::ACHAT_LICENCE_ADULTE => ['data-show-on-select' => true],
                                ProductSelectionForm::ACHAT_LICENCE_ENFANT => ['data-show-on-select' => true]
                            ]
                        ]
                    )
                ?>
                <div id="container-assurance" style="margin-left: 3em;">
                    <?= $form->field($model, 'assurance_extra')
                        ->checkbox([
                            'label' => "Assurance optionnelle"
                    ])?>
                </div>

                <?= $form->field($model, 'inscription_sorano')
                    ->checkbox([
                        'label' => "Inscription Espace Sorano"
                ])?>


                <div class="well justif-wrapper">
                    <?= $form->field($model, 'justif_attestation')
                        ->checkbox([
                            'label' => "A fourni une attestation",
                            'labelOptions' => [ 'class' => 'light-font']
                    ])?>
                    <?= $form->field($model, 'justif_certificat')
                        ->checkbox([
                            'id'            => 'chk_justif_certificate',
                            'label' => "A fourni un certificat médical",
                            'labelOptions' => [ 'class' => 'light-font'],
                            'data-target-id' => 'certif_validity_date'
                    ])?>
                    <div id="certif_validity_date">
                        <?= $form->field($model, "certif_valid_date_start")
                            ->label('valid from', ['class' => 'light-font'])
                            ->textInput([
                                'class'         => 'transaction-reference_date form-control', 
                                'maxlength'     => true, 
                                'autocomplete'  => 'off',
                                'placeholder'   => \Yii::t('app', 'Ex: 23/01/2001')
                            ])
                        ?>                    
                        <?= $form->field($model, "certif_valid_date_end")
                            ->label('valid until', ['class' => 'light-font'])
                            ->textInput([
                                'class'         => 'transaction-reference_date form-control', 
                                'maxlength'     => true, 
                                'autocomplete'  => 'off',
                                'placeholder'   => \Yii::t('app', 'Ex: 23/01/2001'),
                            ])
                        ?>                    
                    </div>
                </div>
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
                    <?php foreach ($model->getCoursProductModels() as $coursId => $cours) :?>
                        <div class="selected-product-item" data-item-id="<?= $coursId ?>">
                            <div class="product-remove" title="remove">
                                <span data-action="remove" class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            </div>

                            <span class="product-name">
                                <span class="glyphicon glyphicon-gift" aria-hidden="true"></span> <?= Html::encode($cours->name) ?>
                            </span>

                            <?php if (!empty($cours->short_description)): ?>
                                <span class="product-short-description">
                                    <?= Html::encode($cours->short_description) ?>
                                </span>
                            <?php endif; ?>

                            <input type="hidden" name="ProductSelectionForm[cours_ids][]" value="<?= $coursId ?>">
                        </div>
                    <?php endforeach; ?>
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