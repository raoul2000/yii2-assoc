<?php
namespace app\components\actions\attachments;

use Yii;
use yii\base\Action;
use app\models\Attachment;
use yii\web\NotFoundHttpException;
use app\components\Constant;

class UpdateAction extends Action
{
    public function run($id, $redirect_url)
    {

        if (($attachment = Attachment::findOne($id)) !== null) {
            if ($attachment->load(Yii::$app->request->post()) && $attachment->save()) {
                return $this->controller->redirect($redirect_url);
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        Yii::$app->params[Constant::PARAM_FLUID_LAYOUT] = true;
        return $this->controller->render('/common/update-attachment', [
            'model' => $attachment,
            'redirect_url' => $redirect_url
        ]);
    }
}
