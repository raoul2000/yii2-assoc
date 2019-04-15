<?php
namespace app\components\actions\orders;

use yii\base\Action;
use app\models\Order;
use yii\web\NotFoundHttpException;

class DeleteAction extends Action
{
    public function run($id, $redirect_url = null)
    {
        if (($model = Order::findOne($id)) !== null) {
            $model->delete();
            if ($redirect_url !== null) {
                return $this->controller->redirect($redirect_url);
            }
            return $this->controller->redirect(['index']);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
