<?php

namespace app\modules\gymv\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $dataFile;

    public function rules()
    {
        return [
            [['dataFile'], 'file', 'skipOnEmpty' => false], //, 'extensions' => 'csv'],
        ];
    }
    
    public function upload($filepath)
    {
        if ($this->validate()) {
            $this->dataFile->saveAs($filepath);
            return true;
        } else {
            return false;
        }
    }
}
