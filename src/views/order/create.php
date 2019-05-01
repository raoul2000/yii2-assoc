<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Create Order';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">
    <?php if (isset($transaction) && $transaction !== null): ?>
        <p>
            <?= Html::a(
                '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back To Transaction N°' . $transaction->id,
                ['transaction/view', 'id' => $transaction->id]
            ) ?>
        </p>
    <?php endif; ?>
    <h1>
        <?= Html::encode($this->title) ?>
        <?php if ($toContact != null): ?>
            <small>
                for <?= Html::a(Html::encode($toContact->name), ['contact/view', 'id' => $toContact->id], ['title' => 'view contact']) ?>
            </small>
        <?php endif; ?>
    </h1>
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
        'products' => $products,
        'contacts' => $contacts,
        'transaction' => $transaction,
        'toContact' => $toContact
    ]) ?>
</div>
