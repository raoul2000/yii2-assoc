<?php

namespace app\models\forms;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile[]|UploadedFile file attribute
     */
    public $file;

    //public $description;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['file', 'file'],
           // ['description', 'string']
            //ArrayHelper::merge(['file', 'file'], $this->getModule()->rules)
        ];
    }
}
