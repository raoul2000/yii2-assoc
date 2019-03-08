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
    public $controllerNamespace = 'app\controllers';

    public $storePath = '@app/uploads/store';

    public $tempPath = '@app/uploads/temp';

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
        $uploadMetadataForm = Yii::$app->request->post('UploadMetadataForm');
        if (!isset($uploadMetadataForm)) {
            return;
        }
        $metadata = $uploadMetadataForm['description'];


        
        $files = UploadedFile::getInstancesByName($this->inputName);
        $userDirPath =  $this->getUserDirPath();

        if (!empty($files)) {
            foreach ($files as $key => $file) {
                $tmpFilepath = $userDirPath . $file->name;
                if (!$file->saveAs($userDirPath . $file->name)) {
                    throw new \Exception(\Yii::t('yii', 'File upload failed.'));
                }
                $metadata[$tmpFilepath] = $metadata[$key];
            }
        }

        $userTempDir = $userDirPath;
        foreach (FileHelper::findFiles($userTempDir) as $file) {
            if (!$this->attachFile($file, $this->owner, $metadata[$file])) {
                throw new \Exception(\Yii::t('yii', 'File upload failed.'));
            }
        }
        rmdir($userTempDir);
    }

    public function originalSaveUploads($event)
    {
        $files = UploadedFile::getInstancesByName($this->inputName);

        if (!empty($files)) {
            foreach ($files as $file) {
                if (!$file->saveAs($this->getUserDirPath() . $file->name)) {
                    throw new \Exception(\Yii::t('yii', 'File upload failed.'));
                }
            }
        }

        $userTempDir = $this->getUserDirPath();
        foreach (FileHelper::findFiles($userTempDir) as $file) {
            if (!$this->attachFile($file, $this->owner)) {
                throw new \Exception(\Yii::t('yii', 'File upload failed.'));
            }
        }
        rmdir($userTempDir);
    }

    public function deleteUploads($event)
    {
        foreach ($this->getFiles() as $file) {
            $this->detachFile($file->id);
        }
    }

    /**
     * @return Attachment[]
     * @throws \Exception
     */
    public function getFiles()
    {
        $fileQuery = Attachment::find()
            ->where([
                'itemId' => $this->owner->id,
                'model' => $this->getShortClass($this->owner)
            ]);
        $fileQuery->orderBy(['id' => SORT_ASC]);

        return $fileQuery->all();
    }

    public function getStorePath()
    {
        return \Yii::getAlias($this->storePath);
    }

    public function getTempPath()
    {
        return \Yii::getAlias($this->tempPath);
    }

    /**
     * @param $fileHash
     * @return string
     */
    public function getFilesDirPath($fileHash)
    {
        $path = $this->getStorePath() . DIRECTORY_SEPARATOR . $this->getSubDirs($fileHash);

        FileHelper::createDirectory($path);

        return $path;
    }

    public function getSubDirs($fileHash, $depth = 3)
    {
        $depth = min($depth, 9);
        $path = '';

        for ($i = 0; $i < $depth; $i++) {
            $folder = substr($fileHash, $i * 3, 2);
            $path .= $folder;
            if ($i != $depth - 1) {
                $path .= DIRECTORY_SEPARATOR;
            }
        }

        return $path;
    }

    public function getUserDirPath()
    {
        \Yii::$app->session->open();

        $userDirPath = $this->getTempPath() . DIRECTORY_SEPARATOR . \Yii::$app->session->id;
        FileHelper::createDirectory($userDirPath);

        \Yii::$app->session->close();

        return $userDirPath . DIRECTORY_SEPARATOR;
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
    public function attachFile($filePath, $owner, $metadata = [])
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
        $fileDirPath = $this->getFilesDirPath($fileHash);
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
        $filePath = $this->getFilesDirPath($file->hash) . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;
        
        // this is the important part of the override.
        // the original methods doesn't check for file_exists to be

        return file_exists($filePath) ? unlink($filePath) && $file->delete() : $file->delete();
    }
}