<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use \app\components\helpers\DateRangeHelper;
use \app\components\helpers\DateHelper;

class DateRangeForm extends Model
{
    public $configuredDateRangeId;
    /**
     * @var date start date (ex: 2019-12-31)
     */
    public $start;
    /**
     * @var date end date (ex: 2019-01-22)
     */
    public $end;
    /**
     * @return array the validation rules.
     */
    private $_configuredDateRanges = [];

    public function init()
    {
        if (array_key_exists('dateRange', Yii::$app->params)) {
            $this->_configuredDateRanges =  Yii::$app->params['dateRange'];
        }
    }

    public function rules()
    {
        return [
            ['configuredDateRangeId', 'validateConfiguredDateRange'],
            [['start', 'end'], 'date', 'format' => Yii::$app->params['dateValidatorFormat']],

            // use standalone validator but redefined date range attribute names 
            ['start', \app\components\validators\DateRangeValidator::className(),
                'startDateAttributeName' => 'start',
                'endDateAttributeName' => 'end'
            ],

        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'configuredDateRangeId' => \Yii::t('app', 'Configured Date Range'),
            'start' => \Yii::t('app', 'Range Start'),
            'end' => \Yii::t('app', 'Range End'),
        ];
    }
    public function validateConfiguredDateRange($attribute, $params, $validator)
    {
        $valueToValidate = $this->$attribute;
        if (!empty($valueToValidate)) {
            if (!array_key_exists($valueToValidate, $this->_configuredDateRanges)) {
                $this->addError($attribute, 'Invalid date range selected');
            } else {
                $start = DateRangeHelper::evaluateConfiguredRangeValue($this->_configuredDateRanges[$valueToValidate]['start']);
                $end   = DateRangeHelper::evaluateConfiguredRangeValue($this->_configuredDateRanges[$valueToValidate]['end']);

                $this->start = DateHelper::toDateAppFormat($start);
                $this->end   = DateHelper::toDateAppFormat($end);
            }
        }
    }

    private function getRangeValue($arg)
    {
        if (is_string($arg)) {
            return $arg;
        } else {
            $reflection = new \ReflectionFunction($arg);
            if ($reflection->isClosure()) {
                return $arg();
            } else {
                throw new Exception('date range configured is neither a string nor a function');
            }
        }
    }
    /**
     * Read Only property that holds the list of all configured date ranges
     * The values are loaded and then cached for further read. If not date range is configured
     * an empty array is returned
     *
     * @return array
     */
    public function getConfiguredDateRanges()
    {
        $dateRangeOptions = [];
        foreach ($this->_configuredDateRanges as $rangeName => $range) {
            $dateRangeOptions[$rangeName] = $rangeName;
        }
        return $dateRangeOptions;
    }
}
