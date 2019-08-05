<?php

namespace app\components\validators;

use yii\validators\Validator;
use \app\components\helpers\DateHelper;

class DateRangeValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if ($model->hasErrors('valid_date_start') || $model->hasErrors('valid_date_end')) {
            return;
        }
        if (!empty($model->valid_date_end) && !empty($model->valid_date_start)) {

            $start = DateHelper::toDateDbFormat($model->valid_date_start);
            $end   = DateHelper::toDateDbFormat($model->valid_date_end);
    
            if (strtotime($end) < strtotime($start)) {
                $this->addError($model, 'valid_date_start', 'Please give correct Start and End dates');
                $this->addError($model, 'valid_date_end', 'Please give correct Start and End dates');
            }
        }
    }
}