<?php
namespace app\components\actions\orders;

use yii\base\Action;
use app\models\Order;
use yii\web\NotFoundHttpException;

class DeleteAction extends Action
{
    public function run($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            $transaction_id = $model->transaction_id;
            
            $model->delete();

            switch ($this->controller->id) {
                case 'transaction':
                    return $this->controller->redirect(['transaction/view', 'id' => $transaction_id]);
                break;
                default:
                    return $this->controller->redirect(['index']);
            }
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
