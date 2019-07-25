<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Transaction */

$this->title = 'complete';
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$valueSumRowClass = '';
$withOrdersAndTransactions = false;
if (count($orders) && count($transactions)) {
    $withOrdersAndTransactions = true;
    $valueSumRowClass = ( $transactionValueSum == $orderValueSum ? 'bg-success' : 'bg-warning');
}
?>
<div class="transaction-view">

    <?php if ($withOrdersAndTransactions && $transactionValueSum != $orderValueSum): ?>
        <div class="alert alert-warning" role="alert">
            <b>Attention</b> : La somme de toutes les transactions (<?= $transactionValueSum ?>) 
            n'est pas égale à la somme des commandes (<?= $orderValueSum ?>)
        </div>
    <?php endif; ?>

    <h2>
        <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span>  
        Transactions <small>complete</small>
    </h2>
    <hr/>
    <table class="table table-condensed table-hover">
        <thead>
            <tr>
                <th>N°</th>
                <th>From</th>
                <th>To</th>
                <th>Date</th>
                <th>Type</th>
                <th>Code</th>
                <th>Value</th>
                <th></th>
            </tr>
        </thead>        
        <tbody>        
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction->id ?></td>
                    <td>
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> ' .
                                Html::encode($bankAccounts[$transaction->from_account_id]),
                            ['bank-account/view', 'id' => $transaction->from_account_id ],
                            ['title' => 'view Account']
                        )?>                                         
                    </td>
                    <td>
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> ' .
                                Html::encode($bankAccounts[$transaction->to_account_id]),
                            ['bank-account/view', 'id' => $transaction->to_account_id ],
                            ['title' => 'view Account']
                        )?>                                         
                    </td>
                    <td><?= $transaction->reference_date ?></td>
                    <td><?= $transactionType[$transaction->type] ?></td>
                    <td><?= $transaction->code ?></td>
                    <td><?= $transaction->value ?></td>
                    <td>
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                            ['transaction/view', 'id' => $transaction->id],
                            ['title' => 'view Transaction']
                        ) ?>                        
                    </td>
                </tr>
            <?php endforeach; ?>    
            <tr class="<?= $valueSumRowClass ?>">
                <td colspan="6" style="text-align:right">
                    Montant Total des transactions :
                </td>
                <td style="border-top-width: 3px;border-top-color: #b1b1b1;text-align:right">
                    <b><?= $transactionValueSum ?></b>
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>    


    <?php if (count($orders) == 0): ?>
        <div class="alert alert-info" role="alert">
            There is no order related to transactions
        </div>
    <?php else: ?>
        <h2>
        <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Orders
        </h2>
        <hr/>    
        <table class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th>Fournisseur</th>
                    <th>Beneficiaire</th>
                    <th>Product</th>
                    <th>Value</th>
                    <th></th>
                </tr>
            </thead>        
            <tbody>        
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <?= Html::a(
                                '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' .
                                    Html::encode($contacts[$order->from_contact_id]),
                                ['contact/view', 'id' => $order->from_contact_id ],
                                ['title' => 'view Contact']
                            )?>
                        </td>
                        <td>
                            <?= Html::a(
                                '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' .
                                    Html::encode($contacts[$order->to_contact_id]),
                                ['contact/view', 'id' => $order->to_contact_id ],
                                ['title' => 'view Contact']
                            )?>
                        </td>
                        <td>
                            <?= Html::a(
                                Html::encode($products[$order->product_id]),
                                ['product/view', 'id' => $order->product_id ],
                                ['title' => 'view Product']
                            )?>                        
                        </td>
                        <td><?= $order->value?></td>
                        <td>
                            <?= Html::a(
                                '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                ['order/view', 'id' => $order->id],
                                ['title' => 'view Order']
                            ) ?>                        
                        </td>
                    </tr>
                <?php endforeach; ?>    
                <tr class="<?= $valueSumRowClass ?>">
                    <td colspan="3" style="text-align:right">
                        Montant Total Des Produits : 
                    </td>
                    <td style="border-top-width: 3px;border-top-color: #b1b1b1;text-align:right">
                        <b><?= $orderValueSum ?></b>
                    </td>
                    <td></td>
                </tr>
            </tbody>        
        </table>
    <?php endif; ?>
</div>
