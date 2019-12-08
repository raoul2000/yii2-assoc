<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Import';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-index">
    <h1>Import</h1>
    <hr/>

    <p>
        <?= Html::a(
            \Yii::t('app', 'I Réseau'), 
            ['/gymv/import/i-reseau'], 
            ['class' => 'btn btn-primary']) 
        ?>    

        <?= Html::a(
            \Yii::t('app', 'Products'), 
            ['/gymv/import/product'], 
            ['class' => 'btn btn-primary']) 
        ?>    

        <?= Html::a(
            \Yii::t('app', 'Exercice'), 
            ['/gymv/import/exercice'], 
            ['class' => 'btn btn-primary']) 
        ?>    
    </p>
</div>
