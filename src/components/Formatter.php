<?php

namespace app\components;

class Formatter extends \yii\i18n\Formatter
{
    public function asGender($value)
    {
        switch ($value) {
            case 1 :
                return \Yii::t('app', 'male');
            case 2 :
                return \Yii::t('app', 'female');
            case 0 :
                return $this->asRaw(null);
        }
    }

    /**
     * Format a value interpreted as the difference between an order value and the sum
     * of all its related transactions.
     *
     * @param int $value
     * @return string
     */
    public function asTransactionValuesDiff($value)
    {
        if ($value === null) {
            return '<span class="label label-default" title="no transaction">no transac.</span>';
        } elseif ($value < 0) {
            return "<span class=\"label label-danger\" title=\"value not covered by transaction(s)\">missing ($value)</span>";
        } elseif ($value > 0) {
            return "<span class=\"label label-warning\" title=\"value more than covered by transaction(s)\">extra (+$value)</span>";
        } else {
            return '<span class="label label-success" title="value exact covered by transaction(s)">covered</span>';
        }
    }

    /**
     * Format a value interpreted as the difference between a transaction's value,
     * and the sum of all its related order values
     *
     * @param int $value
     * @return string
     */
    public function asOrderValuesDiff($value)
    {
        if ($value === null) {
            return '<span class="label label-default" title="no related order">no order</span>';
        } elseif ($value < 0) {
            return "<span class=\"label label-warning\" title=\"all value assigned but not enough to cover orders\">not enough ($value)</span>";
        } elseif ($value > 0) {
            return "<span class=\"label label-danger\" title=\"not all value assigned\">unassigned (+$value)</span>";
        } else {
            return '<span class="label label-success" title="exact value match">complete</span>';
        }
    }
}
