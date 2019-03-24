<?php

namespace app\models\forms;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class DateRangeForm extends Model
{
    /**
     * @var date start date (ex: 2019-12-31)
     */
    public $start_date;
    /**
     * @var date end date (ex: 2019-01-22)
     */
    public $end_date;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'required'],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            [ 'end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>=', 'type' => 'date', 'message' => 'endDate must be after startDate' ]
        ];
    }
}
