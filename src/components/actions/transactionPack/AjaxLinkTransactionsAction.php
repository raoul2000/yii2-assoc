<?php
namespace app\components\actions\transactionPack;

use Yii;
use yii\base\Action;
use app\models\Transaction;
use app\models\TransactionPack;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AjaxLinkTransactionsAction extends Action
{
    public function run()
    {
        if (!Yii::$app->request->isAjax) {
            throw new yii\web\ForbiddenHttpException();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = Yii::$app->request->post();
        $transactionPackId = $data['transactionPackId'];
        $selectedTransactionIds = $data['selectedTransactionIds'];

        $transactionPack =  TransactionPack::findOne($transactionPackId);
        if ($transactionPack == null) {
            throw new NotFoundHttpException('The requested transaction Pack does not exist : id = ' . $transactionPackId);
        }

        if (count($selectedTransactionIds) != 0) {
            foreach ($selectedTransactionIds as $transactionId) {
                $transaction = Transaction::findOne($transactionId);
                if (!isset($transaction)) {
                    throw new NotFoundHttpException('The requested Transaction does not exist : id = '+ $transactionId)  ;
                }
                $transactionPack->link('transactions', $transaction);
            }
        }

        return [
            'success' => true
        ];
    }
}
