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
    public $start_date;
    /**
     * @var date end date (ex: 2019-01-22)
     */
    public $end_date;
    /**
     * @return array the validation rules.
     */
    private $_configuredDateRanges = null;

    public function rules()
    {
        return [
            ['configuredDateRangeId', 'validateConfiguredDateRange'],
            //[['start_date', 'end_date'], 'required'],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            [ 'end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>=', 'type' => 'date', 'message' => 'endDate must be after startDate' ],
        ];
    }

    public function validateConfiguredDateRange($attribute, $params, $validator)
    {
        $valueToValidate = $this->$attribute;
        if (!empty($valueToValidate)) {

            if (!array_key_exists($valueToValidate, $this->getConfiguredDateRanges())) {
                $this->addError($attribute, 'Invalid date range selected');
            } else {
                $ranges = $this->getConfiguredDateRanges();
                $this->start_date = $ranges[$valueToValidate]['start'];
                $this->end_date = $ranges[$valueToValidate]['end'];
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
        if ($this->_configuredDateRanges == null) {
            $this->_configuredDateRanges = $this->loadConfiguredDateRanges();
        }
        return $this->_configuredDateRanges;
    }

    /**
     * Load configured Date Range values.
     * Values are loaded from the Application parameters array
     * 
     * @return array
     */
    private function loadConfiguredDateRanges()
    {
        $configuredDateRanges = [];
        if (array_key_exists('dateRange', Yii::$app->params)) {
            foreach (Yii::$app->params['dateRange'] as $rangeName => $range) {
                $configuredDateRanges[$rangeName] = $rangeName;
            }
        }
        return $configuredDateRanges;
    }
}
