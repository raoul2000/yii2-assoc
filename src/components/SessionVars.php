<?php

namespace app\components;

use Yii;
use app\components\Constant;

/**
 * This class is a wrapper around specific session variables :
 * - *dateRange*
 * - *contact* and related *bank account*
 */
class SessionVars
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
     * Returns the current date range info or NULL if not date range is defined.
     * If an array is returned, it contains 2 items : the start and the end date.
     * example : `[ '2019-01-31', '2019-12-31']`
     *
     * @return NULL|array
     */
    public static function getDateRange()
    {
        $session = Yii::$app->session;

        // no date range found in session : do nothing and return
        if (!$session->has(Constant::SESS_PARAM_NAME_DATERANGE)) {
            return null;
        }
        $dateRange = $session[Constant::SESS_PARAM_NAME_DATERANGE];

        return [
            $dateRange[Constant::SESS_PARAM_NAME_STARTDATE],
            $dateRange[Constant::SESS_PARAM_NAME_ENDDATE],
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
        if (in_array('reference_date', $attributeNames)) {
            $query->andWhere(['between', 'reference_date', $startDate, $endDate]);
        }
        return $queryOrDataprovider;
    }

    public static function clearContact()
    {
        Yii::$app->session->remove(Constant::SESS_PARAM_NAME_CONTACT);
    }

    public static function setContact($id, $name)
    {
        $session = Yii::$app->session;
        $session[Constant::SESS_PARAM_NAME_CONTACT] = [
            'id' => $id,
            'name' => $name
        ];
    }
    
    public static function getContactId()
    {
        $session = Yii::$app->session;
        if ($session->has(Constant::SESS_PARAM_NAME_CONTACT)) {
            return $session[Constant::SESS_PARAM_NAME_CONTACT]['id'];
        }
        return null;
    }

    public static function getContactName()
    {
        $session = Yii::$app->session;
        if ($session->has(Constant::SESS_PARAM_NAME_CONTACT)) {
            return $session[Constant::SESS_PARAM_NAME_CONTACT]['name'];
        }
        return null;
    }

    public static function clearBankAccount()
    {
        Yii::$app->session->remove(Constant::SESS_PARAM_NAME_BANK_ACCOUNT);
    }

    public static function setBankAccount($id, $name)
    {
        $session = Yii::$app->session;
        $session[Constant::SESS_PARAM_NAME_BANK_ACCOUNT] = [
            'id' => $id,
            'name' => $name
        ];
    }

    public static function getBankAccountId()
    {
        $session = Yii::$app->session;
        if ($session->has(Constant::SESS_PARAM_NAME_BANK_ACCOUNT)) {
            return $session[Constant::SESS_PARAM_NAME_BANK_ACCOUNT]['id'];
        }
        return null;
    }

    public static function getBankAccountName()
    {
        $session = Yii::$app->session;
        if ($session->has(Constant::SESS_PARAM_NAME_BANK_ACCOUNT)) {
            return $session[Constant::SESS_PARAM_NAME_CONTACT]['name'];
        }
        return null;
    }
}
