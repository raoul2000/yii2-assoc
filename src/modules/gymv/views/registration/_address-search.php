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
        <small class="wizard-step"><?= \Yii::t('app', 'step') ?> 2/5</small>
    </h3>

    <hr/>

    <form class="row">
        <div class="form-group col-xs-7" style="padding-right:0px">
            <input type="text" class="form-control" id="address" placeholder="<?= \Yii::t('app', 'Enter the address ...') ?>">
        </div>
        <div class="form-group col-xs-3" style="padding-left:0px">
            <input type="text" class="form-control" id="city" placeholder="<?= \Yii::t('app', 'City name') ?>">
        </div>
        <button id="btn-search-address" type="button" class="btn btn-default">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> 
            <?= \Yii::t('app', 'Search') ?>
        </button>
    </form>

    <div id="search-result-container">
        <div id="address-search-result-list">
        </div>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'address-search-form', 
        'enableClientValidation' => false]); 
    ?>

        <input type="hidden" id="search-address" name="search_address" value="" />
        <input type="hidden" id="search-city" name="search_city" value="" />

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
                        'id' => 'btn-address-search-next', 
                        'class' => 'btn btn-primary', 
                        'disabled' => false 
                    ]
            )?>
        </div>

    <?php ActiveForm::end(); ?>    

    
</div>