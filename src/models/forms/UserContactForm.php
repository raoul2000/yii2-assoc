<?php

namespace app\models\forms;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class UserContactForm extends Model
{
    public $contact_id;
    public $bank_account_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['contact_id'], 'required'],
        ];
    }
}
