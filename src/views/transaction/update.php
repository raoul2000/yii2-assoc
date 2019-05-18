<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Transaction */

$this->title = 'Update Transaction N°' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'N°' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="transaction-update">

    <h1>
        <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
        'bankAccounts' => $bankAccounts,
        'products' => $products,
        'categories' => $categories
    ]) ?>

</div>
