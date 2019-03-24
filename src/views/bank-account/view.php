<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\BankAccount */

$this->title = $model->longName;
$this->params['breadcrumbs'][] = ['label' => 'Bank Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bank-account-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Create Another Bank Account', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Transactions From', ['transaction/index', 'TransactionSearch[from_account_id]' => $model->id], ['class' => 'btn btn-default']) ?>
        <?= Html::a('Transactions To', ['transaction/index', 'TransactionSearch[to_account_id]' => $model->id], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Contact',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(
                        Html::encode($model->contact->name),
                        ['contact/view', 'id' => $model->contact->id],
                        ['title' => 'view Contact']
                    );
                }
            ],
            'name',
            'initial_value',
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],

        ],
    ]) ?>

</div>
