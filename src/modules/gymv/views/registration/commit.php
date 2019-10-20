<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
//$this->registerJs(file_get_contents(__DIR__ . '/address.js'), View::POS_READY, 'registration-address');
?>
<div>
    <div class="alert alert-success">
        <b>Registration done ! Good job ... </b><br/>
        Click on the link below to see the information about this contact.
    </div>

    <div class="row">
        <div class="col-xs-6 col-xs-offset-3">
            View profile ... 
            <?= Html::a('<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                . html::encode($contact->longName),
                ['/contact/view', 'id' => $contact->id],
                [
                    "style" => 'font-size: 2em;',
                    'title' => 'view contact'
                ])
            ?>
            <br/>
            or, <b><?= Html::a(
                'Register other Member', 
                ['/gymv/registration/contact-search']
            )?></b>
        </div>
    </div>
</div>