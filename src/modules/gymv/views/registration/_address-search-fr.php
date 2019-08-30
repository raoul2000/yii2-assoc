<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contact;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
?>
<div id="wiz-contact">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= \dosamigos\selectize\SelectizeDropDownList::widget([
            'name' => 'contactId',
            'id' => 'selectized-address',
            'queryParam' => 'q',
            'options' => ['class' => 'form-control'],
            'clientOptions' => [
                'placeholder' => \Yii::t('app', 'enter address to search ...'),
                'create' => false,
                'loadThrottle' => 500,
                'highlight' => false,
                'labelField' => 'label',
                'valueField' => 'label',
                'searchField' => 'name',                           
                'load' => new \yii\web\JsExpression("
                    function(query, callback) {
                        if (!query.length) return callback();
                        $.ajax({
                            url: 'https://api-adresse.data.gouv.fr/search?q=' + encodeURIComponent(query),
                            type: 'GET',
                            error: function() {
                                callback();
                            },
                            success: function(res) {
                                const results = res.features.map( feat => ({
                                    label : feat.properties.label,
                                    name : feat.properties.label
                                }));
                                callback(results);
                            }
                        });                        
                    }
                "), 
                'render' => [
                    'option' => new \yii\web\JsExpression("
                        function(item, escape) {
                            return '<div>' 
                                + '<span class=\"address\">'
                                    + '<span class=\"glyphicon glyphicon-home\" aria-hidden=\"true\"></span>' 
                                        + escape(item.label)
                                    + '</span>'
                                + '</span>' 
                            + '</div>';
                        }
                    ")
                ],
            ],
        ]); ?>        

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