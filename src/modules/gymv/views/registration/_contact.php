<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$this->registerJs(file_get_contents(__DIR__ . '/contact.js'), View::POS_READY, 'registration-contact');
?>
<div id="wiz-contact">
    <h2>Contact</h2>

    <div id="contact-search-container">

        <form id="contact-search-form" class="form-inline">
            <div class="form-group">
                <input type="text" class="form-control" id="contact-name" placeholder="Nom">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" id="contact-firstname" placeholder="Prénom">
            </div>
            <button id="btn-search-contact" class="btn btn-default" type="button">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search
            </button>
        </form>

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