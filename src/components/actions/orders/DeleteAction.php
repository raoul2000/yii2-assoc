<?php
namespace app\components\actions\orders;

use yii\base\Action;
use app\models\Order;
use yii\web\NotFoundHttpException;

class DeleteAction extends Action
{
    public function run($id, $redirect_url)
    {
        if (($model = Order::findOne($id)) !== null) {
            $model->delete();
            return $this->controller->redirect($redirect_url);
            /*
            switch ($this->controller->id) {
                case 'transaction':
                    return $this->controller->redirect(['transaction/view']);
                break;
                default:
                    return $this->controller->redirect(['index']);
            }
            */
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
