<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>
<div id="wiz-commit">
    <div class="alert alert-success">
        <?= \Yii::t('app', 'Registration done !') ?> 
    </div>
    <div class="row">
        <div class="col-lg-12" style="text-align:center; font-size:1.5em; margin-bottom:1em; margin-top:2em;">
            <?= \Yii::t('app', 'What do you want to do next ?') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6" style="text-align:center; font-size:1.5em">
            <?= Html::a(
                \Yii::t('app', 'Register other Member'), 
                ['/gymv/registration/contact-search']
            )?>
        </div>    
        <div class="col-xs-6" style="text-align:center; font-size:1.5em">            
            <?= Html::a(
                \Yii::t('app', 'View Contact Info'),
                ['/contact/view', 'id' => $contact->id],
                [
                    'title' => 'view contact'
                ])
            ?>  
            for
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?= html::encode($contact->longName) ?>    
        </div>
    </div>
</div>