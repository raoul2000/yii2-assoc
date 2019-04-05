<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionPack */

$this->title = 'Create Transaction Pack';
$this->params['breadcrumbs'][] = ['label' => 'Transaction Packs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-pack-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'bankAccounts' => $bankAccounts
    ]) ?>

</div>
