<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BankAccount */

$this->title = 'Update Bank Account: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Bank Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bank-account-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'contacts' => $contacts
    ]) ?>

</div>
