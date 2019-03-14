<?php
namespace app\components\actions\attachments;

use Yii;
use yii\base\Action;
use app\models\Attachment;
use yii\web\NotFoundHttpException;

class CreateAction extends Action
{
    public function run($id, $redirect_url)
    {
        if ($this->controller->hasMethod('findModel')) {
            $ownerModel = $this->controller->findModel($id);
        } else {
            throw new \yii\base\Exception('owner controller does not have a findModel method or method is not public');
        }

        $uploadModel = new \app\models\forms\UploadForm();
        if ($uploadModel->load(Yii::$app->request->post()) && $uploadModel->validate()) {
            
            // here we assume that the owner controller has the AttachmentBehavior which
            // provides method 'saveUploads'
            $attachedFiles = $ownerModel->saveUploads(true);
            if (count($attachedFiles) != 0) {
                return $this->controller->redirect($redirect_url);
            } else {
                $uploadModel->addError('file', 'failed to upload file');
            }
        }

        return $this->controller->render('/common/upload-file', [
            'model' => $uploadModel,
            'redirect_url' => $redirect_url
        ]);
    }
}
