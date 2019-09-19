<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$this->registerJs(file_get_contents(__DIR__ . '/_address-search.js'), View::POS_READY, 'address-search');
$searchAddressUrl = 'url';
?>
<div id="wiz-address-search">

    <h3>
        <span class="glyphicon glyphicon-home" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Address') ?>
        <small class="wizard-step">step 2/5</small>
    </h3>

    <hr/>

    <form class="row">
        <div class="form-group col-xs-7" style="padding-right:0px">
            <input type="text" class="form-control" id="address" placeholder="Enter the address ...">
        </div>
        <div class="form-group col-xs-3" style="padding-left:0px">
            <input type="text" class="form-control" id="city" placeholder="City name">
        </div>
        <button id="btn-search-address" type="button" class="btn btn-default">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> 
            Search
        </button>
    </form>
    <div id="search-result-container">
        <div id="address-search-result-list">
        </div>
    </div>

    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
        <input type="hidden" id="address-record_id" name="address_id" value="" />

        <?= $form
            ->field($model, 'line_1')
            ->hiddenInput([ 'maxlength' => true,  'autocomplete'=>'off'])
            ->label(false) 
        ?>

        <?= $form
            ->field($model, 'zip_code')
            ->hiddenInput(['maxlength' => true, 'autocomplete'=>'off'])
            ->label(false) 
        ?>
        <?= $form
            ->field($model, 'city')
            ->hiddenInput(['maxlength' => true, 'autocomplete'=>'off'])
            ->label(false) 
        ?>

        <?= $form
            ->field($model, 'country')
            ->hiddenInput(['maxlength' => true, 'autocomplete'=>'off'])
            ->label(false) 
        ?>

        <hr />
        <div class="form-group">
            <?= Html::a(
                '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> ' . \Yii::t('app', 'Previous'),
                ['contact-edit'],
                ['class' => 'btn btn-primary']
            )?>

            <?= Html::submitButton(
                    \Yii::t('app', 'Next') . ' <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>', 
                    [
                        'id' => 'btn-address-found', 
                        'class' => 'btn btn-primary', 
                        'disabled' => false 
                    ]
            )?>
        </div>

    <?php ActiveForm::end(); ?>    

    
</div>