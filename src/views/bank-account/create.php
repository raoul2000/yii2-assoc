<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BankAccount */

$this->title = 'Create Bank Account';
$this->params['breadcrumbs'][] = ['label' => 'Bank Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-account-create">

    <h1>
        <?= Html::encode($this->title) ?>
        <?php if ($contact): ?>
            <small>
                for <?= Html::a(
                    Html::encode($contact->longName),
                    ['contact/view', 'id' => $contact->id],
                    ['title' => 'view contact']
                )?>
            </small>
        <?php endif; ?>
    </h1>
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
        'contact' => $contact,
        'contacts' => $contacts
    ]) ?>

</div>
