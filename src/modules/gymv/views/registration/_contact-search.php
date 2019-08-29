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

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= \dosamigos\selectize\SelectizeDropDownList::widget([
            'name' => 'contactId',
            'id' => 'selectized-contact',
            'loadUrl' => ['/api/contact/search'],
            'queryParam' => 'name',
            'options' => ['class' => 'form-control'],
            'clientOptions' => [
                'placeholder' => \Yii::t('app', 'enter contact name to search ...'),
                'create' => false,
                'valueField' => 'id',
                'labelField' => 'fullname',
                'searchField' => 'name',                            
                'render' => [
                    'option' => new \yii\web\JsExpression("
                        function(item, escape) {
                            return '<div>' 
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

        <?= Html::submitButton(
            \Yii::t('app', 'Continue ...'), 
            [
                'id' => 'btn-contact-found', 
                'class' => 'btn btn-default', 
                'disabled' => false 
            ]
        )?>

    <?php ActiveForm::end(); ?>    
    
</div>