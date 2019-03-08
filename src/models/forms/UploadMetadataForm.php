<?php

namespace app\models\forms;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class UploadMetadataForm extends Model
{
    public $description;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['description', 'string']
        ];
    }
}
