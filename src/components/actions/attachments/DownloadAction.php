<?php
namespace app\components\actions\attachments;

use Yii;
use yii\base\Action;
use app\models\Attachment;
use yii\web\NotFoundHttpException;

class DownloadAction extends Action
{
    public function run($id)
    {
        if (($file = Attachment::findOne(['id' => $id])) !== null) {

            $filesDirPath = \Yii::$app->attachmentStorageManager->getFilesDirPath($file->hash);

            $filePath = $filesDirPath . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;
            return Yii::$app->response->sendFile($filePath, "$file->name.$file->type");
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
