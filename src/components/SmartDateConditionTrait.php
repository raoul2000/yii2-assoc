<?php

namespace app\components;

use Yii;
use \app\components\helpers\DateHelper;

trait SmartDateConditionTrait
{

    public function addSmartDateCondition($attributeName, $model, $colName = null)
    {
        if (!empty($model->{$attributeName})) {

            $colName = $colName === null ? $attributeName : $colName;
            
            if ( preg_match('/^(<|>|<=|>=)? *([0-9].+)$/', $model->{$attributeName}, $matches) ) {
                $operator = trim($matches[1]);  // empty string if not set
                $operand =  trim($matches[2]);
            } else {
                $model->addError($attributeName, 'invalid filter');
            }

            // validate operand
            if (!$model->hasErrors()) {
                $dynModel = \yii\base\DynamicModel::validateData(['date' => $operand], [
                    ['date', 'date', 'format' => \Yii::$app->params['dateValidatorFormat']]
                ]);
                if ($dynModel->hasErrors()) {
                    $model->addError($attributeName, $dynModel->getFirstError('date'));
                }           
            }

            // build the query
            if (!$model->hasErrors()) {
                if( !empty($operator)) {
                    $this->andWhere([
                        $operator, $colName, DateHelper::toDateDbFormat($operand)
                    ]);
                } else {
                    $this->andWhere([
                        $colName => DateHelper::toDateDbFormat($operand)
                    ]);
                }
            }
            return $this;
        }
    }  
}
