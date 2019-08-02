<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\quality\controllers\BaseController;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
\yii\web\YiiAsset::register($this);


$script = <<< JS
    document.getElementById('qa-view-selector').addEventListener('click', (ev) => {
        ev.preventDefault();
        const overlay = document.getElementById('qa-view-overlay');
        const content = document.getElementById('qa-view-content');
        const anchor = ev.target.closest('a');

        // update selection on left menu
        this.querySelectorAll('a.list-group-item').forEach( item => item.classList.remove('active'));
        anchor.classList.toggle('active');

        // show overlay
        overlay.style.display = 'block';
        $.get(`\${anchor.href}&ajax=1`)
            .done( (resp) => {
                content.innerHTML = resp;                
            })
            .always( () => {
                overlay.style.display = 'none';
            })
            .fail( () => {
                console.error('error');
            });
    });
JS;
$this->registerJs($script);

?>
<div class="quality-contact-view">

    <h1>
        <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> 
        Quality Check <small><?= Html::encode($pageSubHeader) ?></small>
    </h1>
    <hr/>

    <div class="row">
            <div id="qa-view-overlay">
                <div>Loading ...</div>
            </div>          
        <div class="col-sm-3">
            <div id="qa-view-selector" class="list-group">
                <?php if (in_array(BaseController::VIEW_ANALYSIS, $supportedViews)) : ?>
                    <?= Html::a(
                        ' <h4 class="list-group-item-heading">Analyse</h4>'
                        . ' <p class="list-group-item-text"><small>recherche des incohérences potentielles</small></p>',
                        ['index', 'tab' => BaseController::VIEW_ANALYSIS],
                        ['class' => 'list-group-item ' . ($tab == BaseController::VIEW_ANALYSIS ? 'active' : '')]
                    )?>
                <?php endif; ?>

                <?php if (in_array(BaseController::VIEW_SIMILARITY, $supportedViews)) : ?>
                    <?= Html::a(
                        ' <h4 class="list-group-item-heading">Similarity</h4>'
                        . ' <p class="list-group-item-text"><small>Valeurs presque identiques</small></p>',
                        ['index', 'tab' => BaseController::VIEW_SIMILARITY],
                        ['class' => 'list-group-item ' . ($tab == BaseController::VIEW_SIMILARITY ? 'active' : '')]
                    ) ?>
                <?php endif; ?>
            </div>             
        </div>
        <style>
            #qa-view-overlay {
                position: absolute; /* Sit on top of the page content */
                display: none; /* Hidden by default */
                width: 100%; /* Full width (cover the whole page) */
                height: 100%; /* Full height (cover the whole page) */
                top: 0; 
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(255, 255, 255, 0.8); /* Black background with opacity */
                z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
                cursor: pointer; /* Add a pointer on hover */
            }        
            #qa-view-overlay > div {
                position: absolute;
                top: 50%;
                left: 50%;
                font-size: 50px;
                color: darkgrey;
                transform: translate(-50%,-50%);
                -ms-transform: translate(-50%,-50%);
            }
        </style>
        <div class="col-sm-9">
   
            <div id="qa-view-content">       
                <?= $qaView ?>    
            </div>
        </div>
    </div>

</div>
