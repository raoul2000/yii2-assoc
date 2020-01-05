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
            \Yii::t('app', 'export'), 
            ['/gymv/export/home/index', 'action' => 'export'], 
            ['class' => 'btn btn-primary']) 
        ?>    
    </p>
</div>
