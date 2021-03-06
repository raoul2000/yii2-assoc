<?php

namespace app\components;

use Yii;
use app\models\Transaction;
use app\models\TransactionPack;
use app\models\BankAccount;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;

class BankBookBuilder extends yii\base\BaseObject
{
    /**
     * Creates and returns the bank book for the given bank account
     * **WARNING** : in its current implementation, this method only works if
     * it processes ALL transactions for the bank account
     * @param int $bankAccountId
     * @return array
     */
    public function build($bankAccountId)
    {
        // load bank account model
        $bankAccount = BankAccount::findOne($bankAccountId);
        if ($bankAccount === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        // build the query to find all transactions (debit/credit) for this bank account with their
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

            // ignore 0 value transactions
            if ($transaction['value'] == 0) {
                continue;
            }
            $isDebit = ($transaction['from_account_id'] == $bankAccountId ? true : false);
            $packId = $transaction['transaction_pack_id'];

            if ($packId === null) {
                // this transaction is not included in a pack : add it to the book
                $transactionLines[] = [
                    'date' => $transaction['reference_date'],
                    'type' => $transaction['type'],
                    'code' => $transaction['code'],
                    'description' => $transaction['description'],
                    'debit' => ( $isDebit ? $transaction['value'] : 0),
                    'credit' => ( $isDebit ? 0 : $transaction['value']),
                ];
            } else {
                // include the pack
                // WARNING : if the date of the pack is not in the current range, it
                // should be ingored
                $pack = $transaction['pack'];
                if (! array_key_exists($packId, $packLines)) {
                    $packLines[$packId] = [
                        'date' => $pack['reference_date'],
                        'code' => $transaction['code'], // TODO: how to handle pack code/transaction code ?
                        'description' => $pack['name'],
                        'debit' => 0,
                        'credit' => 0
                    ];
                }
                $packLines[$packId]['debit'] += ( $isDebit ? $transaction['value'] : 0);
                $packLines[$packId]['credit'] += ( $isDebit ? 0 : $transaction['value']);
            } // else ignore this transaction as it has been processed with its related pack
        }

        $bankBook = \array_merge($transactionLines, $packLines);
        return $bankBook;
    }
}
