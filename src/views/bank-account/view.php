<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model app\models\BankAccount */

$this->title = $model->longName;
$this->params['breadcrumbs'][] = ['label' => 'Bank Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$bankAccountModel = $model;
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
        <?= Html::a('Transactions Packs', ['transaction-pack/index', 'TransactionPackSearch[bank_account_id]' => $model->id], ['class' => 'btn btn-default']) ?>
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
            [
                'label' => 'Current Value',
                'format' => 'raw',
                'value' => function ($model) use($accountBalance){
                    return '<b>' . Html::encode($accountBalance['value']) . '</b>';
                }
            ],
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

    <h2>Transactions</h2>
    <hr/>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $transactionDataProvider,
        //'filterModel' => $transactionSearchModel,
        'columns' => [
            'reference_date:date',
            'code',
            [
                'attribute' => 'label',
                'format'    => 'html',
                'value'     => function ($transactionModel, $key, $index, $column) {
                    return Html::encode($transactionModel->description);
                }

            ],
            [
                'attribute' => 'Débit',
                'format'    => 'html',
                'value'     => function ($transactionModel, $key, $index, $column) use ($bankAccountModel) {
                    return $transactionModel->from_account_id == $bankAccountModel->id
                        ? $transactionModel->value
                        : '';
                }
            ],
            [
                'attribute' => 'Crédit',
                'format'    => 'html',
                'value'     => function ($transactionModel, $key, $index, $column) use ($bankAccountModel) {
                    return $transactionModel->from_account_id == $bankAccountModel->id
                    ? ''
                    : $transactionModel->value;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'  => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action == 'view') {
                        return Url::to(['transaction/view', 'id' =>  $model->id, 'redirect_url' => Url::current()]);
                    }
                },
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>


</div>
