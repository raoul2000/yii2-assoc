<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$this->registerJs(file_get_contents(__DIR__ . '/_contact.js'), View::POS_READY, 'registration-contact');
?>
<div id="wiz-contact">
    <h2>Contact</h2>

    <div id="contact-search-container">

        <div class="input-group">
            <input id="contact-name-or-email" type="text" class="form-control" placeholder="Name or Email to Search for ....">
            <span class="input-group-btn">
                <button id="btn-search-contact" class="btn btn-default" type="button">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search
                </button>
            </span>
        </div><!-- /input-group -->


        <div id="contact-search-results">
            <div id="contact-result-info">
                3 results
            </div>
            <div id="contact-result-list">
                <div class="contact-result-item">
                    12 rue Defrance<br/>
                    94300 Vincennes    
                </div>
            </div>
        </div>
    </div>

</div>