<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\quality\controllers\BaseController;

/* @var $this yii\web\View */
/* @var $model app\models\Product */


\yii\web\YiiAsset::register($this);

?>
<div class="quality-contact-view">

    <h1>
        <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> 
        Quality Check <small><?= Html::encode($pageSubHeader) ?></small>
    </h1>
    <hr/>

    <div class="row">
        <div class="col-sm-3">
            <div class="list-group">
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
        <div class="col-sm-9">            
            <?= $qaView ?>    
        </div>
    </div>

</div>
