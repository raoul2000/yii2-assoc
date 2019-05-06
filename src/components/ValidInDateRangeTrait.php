<?php

namespace app\components;

trait ValidInDateRangeTrait
{

    /**
     * Add a search condition on the valid_date_start and valid_date_end date columns
     *
     * @param string $startDate
     * @param string $endDate
     * @return void
     */
    public function validInDateRange($startDate, $endDate = null)
    {
        // no date range provided : return the query unchanged
        if (empty($startDate) && empty($endDate)) {
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
        if (!empty($rangeStart)) {
            $conditions[] = [
                'or',
                ['IS', 'valid_date_end', $NULL],
                ['>=', 'valid_date_end', $rangeStart],
            ];
        }
        if (!empty($rangeEnd)) {
            $conditions[] = [
                'or',
                ['IS', 'valid_date_start', $NULL],
                ['<=', 'valid_date_start', $rangeEnd],
            ];
        }

        // create final WHERE clause
        $whereClause = [];
        if (count($conditions) == 2) {
            $whereClause = [
                'and',
                $conditions[0],
                $conditions[1],
            ];
        } else {
            $whereClause = $conditions[0];
        }
        return $this->andWhere($whereClause);
    }
}
