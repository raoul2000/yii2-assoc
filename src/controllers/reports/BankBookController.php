<?php

namespace app\controllers\reports;

use Yii;
use app\models\Transaction;
use app\models\TransactionPack;
use app\models\BankAccount;
use yii\helpers\VarDumper;

class BankBookController extends \yii\web\Controller
{
    public function compareByTimeStamp($item1, $item2)
    {
        $time1 = $item1['date'];
        $time2 = $item2['date'];
        if (strtotime($time1) < strtotime($time2)) {
            return 1;
        } elseif (strtotime($time1) > strtotime($time2)) {
            return -1;
        } else {
            return 0;
        }
    }
    /**
     * Select a bank account to produce the report view from
     *
     * @return mixed
     */
    public function actionIndex_v1()
    {
        $bankBook = [];

        $transactionQuery = Transaction::find()
            ->asArray()
            ->where(['transaction_pack_id' => null]);

        foreach ($transactionQuery->each(10) as $transaction) {
            Yii::info($transaction['id']);
            $bankBook[] = [
                'date' => $transaction['reference_date'],
                'code' => $transaction['code'],
                'description' => $transaction['description'],
                'value' => $transaction['value'],
            ];
        }


        $packQuery = TransactionPack::find()
            ->asArray()
            ->with('transactions');

        foreach ($packQuery->each(10) as $pack) {
            Yii::info('pack : ' . VarDumper::dumpAsString($pack));
            Yii::info('transactions : ' . count($pack['transactions']));
            $transactionValueSum = 0;

            foreach ($pack['transactions'] as $linkedTransaction) {
                $transactionValueSum += $linkedTransaction['value'];
            }

            $bankBook[] = [
                'date' => $pack['reference_date'],
                'code' => 'no code',
                'description' => $pack['name'],
                'value' => $transactionValueSum,
            ];
        }
        
        usort($bankBook , [$this, 'compareByTimeStamp']);
        
        return $this->render('index', [
            'bankBook' => $bankBook
        ]);
    }

    public function actionIndex()
    {
        $builder = new \app\components\BankBookBuilder();

        return $this->render('index', [
            'bankBook' => $builder->build()
        ]);

    }
    public function actionIndex_v2()
    {
        $bankAccountId = 4;
        $builder = new \app\components\BankBookBuilder();
        $builder->build();

        // load bank account model
        $bankAccount = BankAccount::findOne($bankAccountId);
        if ($bankAccount === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        // build the query to find all transactions for this bank account with their
        // related pack
        $transactionQuery = Transaction::find()
            ->where(
                'from_account_id=:from_account_id or to_account_id=:to_account_id',
                [
                    'from_account_id' => $bankAccount->id,
                    'to_account_id' => $bankAccount->id
                ]
            )
            ->with('pack')
            ->asArray();

        // process each transaction (per batch) to create the bank book array
        $transactionLines = [];
        $packLines = [];
        foreach ($transactionQuery->each(10) as $transaction) {
            Yii::info('transaction : ' . VarDumper::dumpAsString($transaction));
            $packId = $transaction['transaction_pack_id'];

            if ($packId === null) {
                // this transaction is not included in a pack : add it to the book
                $transactionLines[] = [
                    'date' => $transaction['reference_date'],
                    'code' => $transaction['code'],
                    'description' => $transaction['description'],
                    'value' => $transaction['value'],
                ];    
            } else {
                // include the pack
                // WARNING : if the date of the pack is not in the current range, it 
                // should be ingored
                $pack = $transaction['pack'];
                if ( ! array_key_exists($packId, $packLines)) {
                    $packLines[$packId] = [
                        'date' => $pack['reference_date'],
                        'code' => $transaction['code'], // TODO: how to handle pack code/transaction code ?
                        'description' => $pack['name'],
                        'value' => $transaction['value']
                    ];
                } else {
                    $packLines[$packId]['value'] += $transaction['value'];
                }
            } // else ignore this transaction as it has been processed with its related pack
        }

        $bankBook = \array_merge($transactionLines, $packLines);
        return $this->render('index', [
            'bankBook' => $bankBook
        ]);
    


    }
    public function actionView($account_id)
    {
        return $this->render('view');
    }

}
