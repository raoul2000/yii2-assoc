<?php

namespace app\components;

use Yii;
use app\components\Constant;

class SessionQueryParams
{
    /**
     * Set the date range values in the current session
     *
     * @param string $start_date
     * @param string $end_date
     * @return void
     */
    public static function setDateRange($start_date, $end_date) 
    {
        $session = Yii::$app->session;
        $session[Constant::SESS_PARAM_NAME_DATERANGE] = [
            Constant::SESS_PARAM_NAME_STARTDATE => $start_date,
            Constant::SESS_PARAM_NAME_ENDDATE => $end_date,
        ];
    }

    /**
     * Remove the date range criteria from the session
     *
     * @return void
     */
    public static function clearDateRange()
    {
        Yii::$app->session->remove(Constant::SESS_PARAM_NAME_DATERANGE);
    }

    /**
     * Modifies the provided object to apply date range criteria.
     * Depending on the $model type, the SQL condition is modified differently.
     *
     * @param mixed $queryOrDataprovider
     * @param object instance of yii\base\Model
     * @return mixed
     */
    public static function applyDateRange($queryOrDataprovider, $model) 
    {
        if ($queryOrDataprovider instanceof \yii\data\ActiveDataProvider) {
            $query = $queryOrDataprovider->query;
        } else {
            $query = $queryOrDataprovider;
        }

        $session = Yii::$app->session;

        // no date range found in session : do nothing and return
        if (!$session->has(Constant::SESS_PARAM_NAME_DATERANGE)) {
            return $queryOrDataprovider;
        }    

        // apply date range criteria to model
        $range = $session->get(Constant::SESS_PARAM_NAME_DATERANGE);
        $startDate = $range[Constant::SESS_PARAM_NAME_STARTDATE];
        $endDate = $range[Constant::SESS_PARAM_NAME_ENDDATE];

        // depending on model's attributes, SQL condition is modified to apply
        // date range criteria
        $attributeNames = array_keys($model->getAttributes());
        if( in_array('reference_date', $attributeNames)) {
            $query->andWhere(['between', 'reference_date', $startDate, $endDate]);
        }
        return $queryOrDataprovider;
    }
}
