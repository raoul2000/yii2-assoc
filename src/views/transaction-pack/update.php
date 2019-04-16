<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionPack */

$this->title = 'Update Transaction Pack: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Transaction Packs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="transaction-pack-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
