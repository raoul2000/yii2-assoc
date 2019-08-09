<?php

namespace app\components;

use app\models\Contact;
use yii\helpers\Html;
use \app\components\helpers\DateHelper;

class Formatter extends \yii\i18n\Formatter
{
    /**
     * Format a tag list provided as a coma separated list of tag names
     *
     * @param string $values coma separated list of tag names
     * @return string the HTMl rendering
     */
    public function asTagsList($values)
    {
        $htmlTags = array_map(function ($tagValue) {
            return '<span class="label label-default" style="font-size:1em;font-weight:normal">' 
                . '<span class="glyphicon glyphicon-tag" aria-hidden="true"></span> '
                . Html::encode(trim($tagValue))
            . '</span>';
        }, explode(',', $values));

        return \implode(' ', $htmlTags);
    }

    public function asGender($value)
    {
        switch ($value) {
            case Contact::GENDER_MALE :
                return \Yii::t('app', 'male');
            case Contact::GENDER_FEMALE :
                return \Yii::t('app', 'female');
            default :
                return $this->asRaw(null);
        }
    }

    public function asClickToCall($value)
    {
        if (!empty($value)) {
            return "<a title=\"call this number\" href=\"tel:${value}\">${value}</a>";
        } else {
            return $this->asRaw(null);
        }
    }
    /**
     * Format a date in format YYYY-MM-DD as an age related to the current date
     *
     * @param string $birthday YYYY-MM-DD 
     * @return void
     */
    public function asAge($birthday)
    {
        if (!empty($birthday)) {
            return \app\components\helpers\DateHelper::computeAge($birthday);
        } else {
            return $this->asRaw(null);
        }
    }

    /**
     * Format a note text as a small information icon where the note is set as the title
     *
     * @param string $note
     * @return void
     */
    public function asNote($note)
    {
        if (empty(trim($note))) {
            return '';
        } else {
            return ' <span class="glyphicon glyphicon-info-sign" style="color: cadetblue;" aria-hidden="true" title="' . Html::encode($note) . '"></span>';
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

    /**
     * Format a Date provided in the App format, into the given format
     * A typical example is when the date is provided with the format dd/mm/yyyy when the format expected by 
     * the base method is yyyy-mm-dd.
     * 
     * @param string $value
     * @param string $format
     * @return void
     */
    public function asAppDate($value, $format = null)
    {
        if (!empty($value)) {
            return $this->asDate(DateHelper::toDateDbFormat($value), $format);
        } else {
            return $this->asRaw(null);
        }
    }
}
