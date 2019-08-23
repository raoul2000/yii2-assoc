<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = 'Create Product';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">

    <h1>
        <span class="glyphicon glyphicon-gift" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories
    ]) ?>

</div>
