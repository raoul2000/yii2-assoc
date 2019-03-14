<?php
namespace app\components\actions\attachments;

use Yii;
use yii\base\Action;
use app\models\Attachment;
use yii\web\NotFoundHttpException;

class PreviewAction extends Action
{
    public function run($id)
    {
        if (($file = Attachment::findOne(['id' => $id])) !== null) {

            $filesDirPath = \Yii::$app->attachmentStorageManager->getFilesDirPath($file->hash);

            $filePath = $filesDirPath . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;

            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->statusCode = 200;
            $headers = Yii::$app->response->headers;
            $headers->set('Content-Type', $file->mime);
            Yii::$app->response->data = file_get_contents($filePath);
            Yii::$app->response->send();
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
