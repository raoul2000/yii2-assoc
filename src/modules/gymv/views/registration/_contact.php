<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$this->registerJs(file_get_contents(__DIR__ . '/_contact.js'), View::POS_READY, 'registration-contact');
$contactSearchServiceUrl = Url::to(['/api/contact/search']);
?>
<div id="wiz-contact">
    <h2>Contact</h2>

    <input id="contact-search-ws-url" type="hidden" value="<?= $contactSearchServiceUrl ?>"/>

    <div id="contact-search-container">

        <div class="input-group">
            <input id="contact-name-to-search" type="text" class="form-control" placeholder="Enter Name to Search for ....">
            <span class="input-group-btn">
                <button id="btn-search-contact" class="btn btn-default" type="button">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search
                </button>
            </span>
        </div><!-- /input-group -->


        <div id="contact-search-results">
            <div id="contact-result-info">
                
            </div>
            <div id="contact-result-list">
            </div>
        </div>
    </div> <!-- / contact-search-container -->

    <div id="contact-form-container">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true, 'autocomplete'=> 'off' ]) ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div> <!-- / contact-form-container -->

</div>