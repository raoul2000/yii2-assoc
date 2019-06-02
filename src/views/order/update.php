<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Update Order: ' . $model->product->name;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->product->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-update">

    <h1>
        <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    
    <?= $this->render('_form', [
        'model' => $model,
        'products' => $products,
        'contacts' => $contacts,
        'toContact' => $toContact
    ]) ?>

</div>
