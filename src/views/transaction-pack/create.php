<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionPack */

$this->title = \Yii::t('app', 'Create Transaction Pack');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Transactions'), 'url' => ['transaction/index']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Transaction Packs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-pack-create">

    <h1>
        <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
        <?php if ($bankAccount): ?>
            <small>
                <?= \Yii::t('app', 'for account') ?> <?= $bankAccount->longName ?>
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
