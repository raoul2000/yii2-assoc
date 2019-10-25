<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$this->registerJs(file_get_contents(__DIR__ . '/address.js'), View::POS_READY, 'registration-address');
?>
<div id="gymv-registration-address">
    <div class="row">
        <div class="col-sm-6">
            <div>
                <h2><?= \Yii::t('app', 'Search') ?></h2>
                <hr/>
                <div class="input-group">
                    <input id="address-search" type="text" class="form-control" placeholder="Enter Address to search ...">
                    <span class="input-group-btn">
                        <button id="btn-search-address" class="btn btn-default" type="button">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> <?= \Yii::t('app', 'Search') ?>
                    </button>
                    </span>
                </div><!-- /input-group -->                
            </div>

            <div id="address-result-info">
                3 results
            </div>
            <div id="address-result-list">
                <div class="address-result-item">
                    12 rue Defrance<br/>
                    94300 Vincennes    
                </div>
            </div>
        </div>


        <div class="col-sm-6">
            <div>
                <h2>Address</h2>
                <hr/>
                <div class="address-form">

                    <?php $form = ActiveForm::begin(); ?>

                        <?php if ($model->hasErrors()) {
                            echo $form->errorSummary($model);
                        }?>

                        <?= $form->field($model, 'line_1')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

                        <?= $form->field($model, 'line_2')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

                        <?= $form->field($model, 'line_3')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

                        <?= $form->field($model, 'zip_code')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

                        <?= $form->field($model, 'city')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

                        <?= $form->field($model, 'country')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>

                        <hr/>
                        
                        <div class="form-group">
                            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                            <?= Html::a('Cancel', $redirect_url, ['class' => 'btn btn-default'])  ?>
                        </div>

                    <?php ActiveForm::end(); ?>

                </div>            
            </div>
        </div><!-- / col 6 -->
        
    </div>
</div>