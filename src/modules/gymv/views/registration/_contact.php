<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
//$this->registerJs(file_get_contents(__DIR__ . '/address.js'), View::POS_READY, 'registration-address');
?>
<div id="wiz-contact">
    <h2>Contact</h2>
    <div class="input-group">
                    <input id="address-search" type="text" class="form-control" placeholder="Enter Address to search ...">
                    <span class="input-group-btn">
                        <button id="btn-search-address" class="btn btn-default" type="button">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search
                    </button>
                    </span>
                </div><!-- /input-group -->                

</div>