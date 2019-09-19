<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$contactSearchServiceUrl = Url::to(['/api/contact/search']);
?>
<div id="wiz-contact">

    <h3>
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Contact') ?>
        <small class="wizard-step">step 1/5</small>
    </h3>

    <hr/>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= \dosamigos\selectize\SelectizeDropDownList::widget([
            'name' => 'contactId',
            'id' => 'selectized-contact',
            'loadUrl' => ['/api/contact/search'],
            'queryParam' => 'name',
            'options' => ['class' => 'form-control'],
            'clientOptions' => [
                'placeholder' => \Yii::t('app', 'enter contact name to search ...'),
                'create' => new \yii\web\JsExpression("
                    function(input) {
                        return { 'id' : 'new-contact@' + input, 'fullname' : input};
                    }
                "),
                'valueField' => 'id',
                'labelField' => 'fullname',
                'searchField' => 'name',                            
                'render' => [
                    'option_create' => new \yii\web\JsExpression("
                        function(data, escape) {
                            return '<div class=\"create rw-selectize-create-item\">Ajouter '+ escape(data.input) +'</div>';
                        }
                    "),
                    'option' => new \yii\web\JsExpression("
                        function(item, escape) {
                            return '<div class=\"rw-selectize-item\">' 
                                + '<span class=\"fullname\">'
                                    + '<span class=\"glyphicon glyphicon-user\" aria-hidden=\"true\"></span>' 
                                        + escape(item.fullname)
                                    + '</span>'
                                + '</span>' 
                            + '</div>';
                        }
                    ")
                ],
                'onChange' => new \yii\web\JsExpression("
                    function(value) {
                        document.getElementById('btn-contact-found').disabled = value.length == 0;
                    }
                ")
            ],
        ]); ?>        

        <hr/>
        
        <?= Html::submitButton(
            \Yii::t('app', 'Next') . ' <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>', 
            [
                'id' => 'btn-contact-found', 
                'class' => 'btn btn-primary', 
                'disabled' => false 
            ]
        )?>

    <?php ActiveForm::end(); ?>    
    
</div>