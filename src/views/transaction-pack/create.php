<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionPack */

$this->title = 'Create Transaction Pack';
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['transaction/index']];
$this->params['breadcrumbs'][] = ['label' => 'Transaction Packs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-pack-create">

    <h1>
        <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
        <?php if ($bankAccount): ?>
            <small>
                for account <?= $bankAccount->longName ?>
            </small>
        <?php endif; ?>
    </h1>
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
        'bankAccount' => $bankAccount,
        'bankAccounts' => $bankAccounts
    ]) ?>

</div>
