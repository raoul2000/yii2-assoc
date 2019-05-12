<?php
namespace app\components\actions\transactions;

use Yii;
use yii\base\Action;
use app\models\Order;
use app\models\Transaction;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AjaxLinkOrdersAction extends Action
{
    public function run()
    {
        if (!Yii::$app->request->isAjax) {
            throw new yii\web\ForbiddenHttpException();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = Yii::$app->request->post();
        $transactionId = $data['transactionId'];
        $selectedOrderIds = $data['selectedOrderIds'];

        $transaction =  Transaction::findOne($transactionId);
        if ($transaction == null) {
            throw new NotFoundHttpException('The requested transaction does not exist : id = ' . $transactionId);
        }

        if (count($selectedOrderIds) != 0) {
            foreach ($selectedOrderIds as $orderId) {
                $order = Order::findOne($orderId);
                if (!isset($order)) {
                    throw new NotFoundHttpException('The requested order does not exist : id = '+ $orderId)  ;
                }
                $transaction->linkToOrder($order);
            }
        }

        return [
            'success' => true
        ];
    }
}
