<?php

namespace app\components\validators;

use yii\validators\Validator;
use \app\components\helpers\DateHelper;

class DateRangeValidator extends Validator
{
    public $startDateAttributeName = 'valid_date_start';
    public $endDateAttributeName = 'valid_date_end';

    public function validateAttribute($model, $attribute)
    {
        if(!$model->hasProperty($this->startDateAttributeName)) {
            throw new \yii\base\UnknownPropertyException('invalid property ' . $this->startDateAttributeName);
        }
        if(!$model->hasProperty($this->endDateAttributeName)) {
            throw new \yii\base\UnknownPropertyException('invalid property ' . $this->endDateAttributeName);
        }
        if ($model->hasErrors($this->startDateAttributeName) || $model->hasErrors($this->endDateAttributeName)) {
            return;
        }
        if (!empty($model->{$this->startDateAttributeName}) && !empty($model->{$this->endDateAttributeName})) {

            // date values are formatted the way user entered them (e.g. dd/mm/yyyy), so don't forget to convert them
            $start = DateHelper::toDateDbFormat($model->{$this->startDateAttributeName});
            $end   = DateHelper::toDateDbFormat($model->{$this->endDateAttributeName});
    
            if (strtotime($end) < strtotime($start)) {
                $this->addError($model, $this->startDateAttributeName, 'Please give correct Start and End dates');
                $this->addError($model, $this->endDateAttributeName, 'Please give correct Start and End dates');
            }
        }
    }
}