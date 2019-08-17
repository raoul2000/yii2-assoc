<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Transaction */

$this->title = \Yii::t('app', 'Create Transaction');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Transactions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-create">

    <h1>
        <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
        <?php if ($model->from_account_id != null): ?>
            <small>
                <?= \Yii::t('app', 'Debit From') ?> <?= Html::a(
                    Html::encode($bankAccounts[$model->from_account_id]),
                    ['bank-account/view', 'id' => $model->from_account_id ],
                    ['title' => \Yii::t('app', 'view bank account')]
                )?>
            </small>
        <?php elseif ($model->to_account_id != null): ?>
        <small><?= \Yii::t('app', 'Credit To') ?> <?= Html::a(
                Html::encode($bankAccounts[$model->to_account_id]),
                ['bank-account/view', 'id' => $model->to_account_id ],
                ['title' => \Yii::t('app', 'view bank account')])
            ?></small>
        <?php endif; ?>
    </h1>
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
        'bankAccounts' => $bankAccounts,
        'products' => $products,
        'order' => $order,
        'categories' => $categories
    ]) ?>

</div>
