<?php

namespace app\components;

trait DateInRangeTrait 
{
    
    /**
     * Add a search condition on the `reference_date` attribute.
     *
     * @param string $startDate
     * @param string $endDate
     * @return void
     */
    public function dateInRange($startDate, $endDate = null)
    {
        // no date range provided : return the query unchanged
        if ( empty($startDate) && empty($endDate)) {
            return $this;
        }

        $rangeStart = $startDate;
        $rangeEnd = $endDate;

        // validate the provided date range. If it is closed, start date must NOT be after end date
        if (!empty($startDate) && !empty($endDate) && \strtotime($rangeStart) > strtotime($rangeEnd)) {
            throw new \yii\base\InvalidCallException('start date range must be lower or equal to end date');
        }

        // create conditions
        $conditions = [];
        $NULL = new \yii\db\Expression('null');
        if ( !empty($rangeStart) && !empty($rangeEnd)) {
            $conditions = [
                'OR',
                ['IS', 'reference_date', $NULL],
                ['BETWEEN', 'reference_date', $startDate, $endDate]
            ];
        } elseif (!empty($rangeStart)) {
            $conditions = [
                'OR',
                ['IS', 'reference_date', $NULL],
                ['>=', 'reference_date', $rangeStart],
            ];
        } elseif (!empty($rangeEnd)) {
            $conditions = [
                'OR',
                ['IS', 'reference_date', $NULL],
                ['<=', 'reference_date', $rangeEnd],
            ];
        }

        return $this->andWhere($conditions);
    }


}