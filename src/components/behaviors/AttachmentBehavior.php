<?php

namespace app\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;
use app\models\Attachment;

class AttachmentBehavior extends Behavior
{
    public $rules = [];

    public $inputName = 'UploadForm[file]';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveUploads',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveUploads',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteUploads'
        ];
    }

    public function saveUploads($event)
    {
        $uploadForm = new  \app\models\forms\UploadForm();
        $uploadForm->load(Yii::$app->request->post());

        $files = UploadedFile::getInstancesByName($this->inputName);
        $userDirPath =  \Yii::$app->attachmentStorageManager->getUserDirPath();

        if (!empty($files)) {
            foreach ($files as $key => $file) {
                $tmpFilepath = $userDirPath . $file->name;
                if (!$file->saveAs($userDirPath . $file->name)) {
                    throw new \Exception(\Yii::t('yii', 'File upload failed.'));
                }
            }
        }

        $userTempDir = $userDirPath;
        foreach (FileHelper::findFiles($userTempDir) as $file) {
            if (!$this->attachFile($file, $this->owner, $uploadForm)) {
                throw new \Exception(\Yii::t('yii', 'File upload failed.'));
            }
        }
        rmdir($userTempDir);
    }

    public function deleteUploads($event)
    {
        foreach ($this->getAttachments() as $file) {
            $this->detachFile($file->id);
        }
    }

    /**
     * @return Attachment[]
     * @throws \Exception
     */
    public function getAttachments()
    {
        $fileQuery = Attachment::find()
            ->where([
                'itemId' => $this->owner->id,
                'model' => $this->getShortClass($this->owner)
            ]);
        $fileQuery->orderBy(['id' => SORT_ASC]);

        return $fileQuery->all();
    }

    public function getShortClass($obj)
    {
        $className = get_class($obj);
        if (preg_match('@\\\\([\w]+)$@', $className, $matches)) {
            $className = $matches[1];
        }
        return $className;
    }

    /**
     * @param $filePath string
     * @param $owner
     * @return bool|Attachment
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function attachFile($filePath, $owner, $uploadForm)
    {
        if (empty($owner->id)) {
            throw new Exception('Parent model must have ID when you attaching a file');
        }
        if (!file_exists($filePath)) {
            throw new Exception("File $filePath not exists");
        }

        $fileHash = md5(microtime(true) . $filePath);
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
        $newFileName = "$fileHash.$fileType";
        $fileDirPath = \Yii::$app->attachmentStorageManager->getFilesDirPath($fileHash);
        $newFilePath = $fileDirPath . DIRECTORY_SEPARATOR . $newFileName;

        if (!copy($filePath, $newFilePath)) {
            throw new Exception("Cannot copy file! $filePath  to $newFilePath");
        }

        $file = new Attachment();
        $file->name = pathinfo($filePath, PATHINFO_FILENAME);
        $file->model = $this->getShortClass($owner);
        $file->itemId = $owner->id;
        $file->hash = $fileHash;
        $file->size = filesize($filePath);
        $file->type = $fileType;
        $file->mime = FileHelper::getMimeType($filePath);

        $file->category_id = $uploadForm->category_id;
        $file->note = $uploadForm->note;

        if ($file->save()) {
            unlink($filePath);
            return $file;
        } else {
            return false;
        }
    }

    public function detachFile($id)
    {
        /** @var Attachment $file */
        $file = Attachment::findOne(['id' => $id]);
        if (empty($file)) {
            return false;
        }
        $filesDirPath = \Yii::$app->attachmentStorageManager->getFilesDirPath($file->hash);
        $filePath = $filesDirPath . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;
        
        // this is the important part of the override.
        // the original methods doesn't check for file_exists to be

        return file_exists($filePath) ? unlink($filePath) && $file->delete() : $file->delete();
    }
}
