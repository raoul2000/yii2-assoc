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

    /**
     * @var int|nul optional attachment category identifier
     */
    public $category_id;
    /**
     * @var string|null optional note describinf the attachment
     */
    public $note;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['file', 'file'],
            ['note', 'string'],
            ['category_id', 'integer'],
        ];
    }
}
