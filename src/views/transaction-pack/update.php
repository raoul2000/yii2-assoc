<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionPack */

$longName = 'N°' . $model->id . ' - ' . $model->name;
$this->title = 'Update Pack: ' . $longName;
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['transaction/index']];
$this->params['breadcrumbs'][] = ['label' => 'Packs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $longName , 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="transaction-pack-update">

    <h1>
        <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
    </h1>

    <hr/>
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
