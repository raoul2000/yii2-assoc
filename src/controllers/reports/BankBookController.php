<?php

namespace app\controllers\reports;

use Yii;
use app\models\Transaction;
use app\models\TransactionPack;
use yii\helpers\VarDumper;

class BankBookController extends \yii\web\Controller
{
    public function compareByTimeStamp($item1, $item2)
    { 
        $time1 = $item1['date'];
        $time2 = $item2['date'];
        if (strtotime($time1) < strtotime($time2)) 
            return 1; 
        else if (strtotime($time1) > strtotime($time2))  
            return -1; 
        else
            return 0; 
    }
    /**
     * Select a bank account to produce the report view from
     *
     * @return mixed
     */
    public function actionIndex()
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

    public function actionView($account_id)
    {
        return $this->render('view');
    }

}
