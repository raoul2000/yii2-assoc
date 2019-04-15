<?php
namespace app\components\actions\attachments;

use Yii;
use yii\base\Action;
use app\models\Attachment;
use yii\web\NotFoundHttpException;

class DeleteAction extends Action
{
    public function run($id, $redirect_url)
    {
        if (($file = Attachment::findOne(['id' => $id])) !== null) {
            $file->delete();
            return $this->controller->redirect($redirect_url);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
