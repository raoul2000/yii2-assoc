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
\yii\web\YiiAsset::register($this);
$transactionValueSum = $orderValueSum = 0;
?>
<div class="transaction-view">
    <h2>
        <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span>  
        Transactions
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
            <?php foreach($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction->id ?></td>
                    <td><?= $bankAccounts[$transaction->from_account_id] ?></td>
                    <td><?= $bankAccounts[$transaction->to_account_id] ?></td>
                    <td><?= $transaction->reference_date ?></td>
                    <td><?= $transactionType[$transaction->type] ?></td>
                    <td><?= $transaction->code ?></td>
                    <td><?= $transaction->value ?></td>
                    <td>
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                            ['transaction/view', 'id' => $transaction->id],
                            ['title' => 'view']
                        ) ?>                        
                    </td>
                    <?php
                        $transactionValueSum += $transaction->value;
                    ?>
                </tr>
            <?php endforeach; ?>    
            <tr style="font-weight:bold; font-size:1.2em;background-color:#eee">
                <td colspan="6" style="text-align:right">
                    Montant Total des transactions :
                </td>
                <td>
                        <?= $transactionValueSum ?>
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>    

    <h2>
        Orders
    </h2>
    <hr/>
    <?php if(count($orders) == 0): ?>
        <div class="alert alert-info" role="alert">
            There is no order related to transactions
        </div>
    <?php else: ?>
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
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td><?= $contacts[$order->from_contact_id]?></td>
                        <td><?= $contacts[$order->to_contact_id]?></td>
                        <td><?= $products[$order->product_id]?></td>
                        <td><?= $order->value?></td>
                        <td>
                            <?= Html::a(
                                '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                ['order/view', 'id' => $order->id],
                                ['title' => 'view']
                            ) ?>                        
                        </td>
                        <?php
                            $orderValueSum += $order->value;
                        ?>
                    </tr>
                <?php endforeach; ?>    
                <tr style="font-weight:bold; font-size:1.2em;background-color:#eee">
                    <td colspan="3" style="text-align:right">
                        Montant Total Des Produits : 
                    </td>
                    <td>
                        <?= $orderValueSum ?>
                    </td>
                    <td></td>
                </tr>
            </tbody>        
        </table>
    <?php endif; ?>
</div>
