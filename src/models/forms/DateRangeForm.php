<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

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
            [['start', 'end'], 'date', 'format' => 'php:Y-m-d'],
            [ 'end', 'compare', 'compareAttribute' => 'start', 'operator' => '>=', 'type' => 'date', 'message' => 'endDate must be after startDate' ],
        ];
    }

    public function validateConfiguredDateRange($attribute, $params, $validator)
    {
        $valueToValidate = $this->$attribute;
        if (!empty($valueToValidate)) {
            if (!array_key_exists($valueToValidate, $this->_configuredDateRanges)) {
                $this->addError($attribute, 'Invalid date range selected');
            } else {
                $this->start = $this->_configuredDateRanges[$valueToValidate]['start'];
                $this->end   = $this->_configuredDateRanges[$valueToValidate]['end'];
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
