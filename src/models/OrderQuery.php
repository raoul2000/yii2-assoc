<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Order]].
 *
 * @see Order
 */
class OrderQuery extends \yii\db\ActiveQuery
{
    /**
     * Apply Date range criteria on the selected orders
     *
     * @param string $startDate
     * @param string $endDate
     * @return void
     */
    public function inDateRange($startDate, $endDate = null)
    {
        if ( empty($startDate)) {
            return $this;
        }
        $rangeStart = $startDate;
        $rangeEnd = ($endDate != null ? $endDate : $startDate);

        if (\strtotime($rangeStart) > strtotime($rangeEnd)) {
            throw new \yii\base\InvalidCallException('start date range must be lower or equal to end date');
        }

        $NULL = new \yii\db\Expression('null');
        return $this->andWhere([
            'and',
            [
                'or',
                ['IS', 'valid_date_start', $NULL],
                ['<=', 'valid_date_start', $rangeEnd],
            ],
            [
                'or',
                ['IS', 'valid_date_end', $NULL],
                ['>=', 'valid_date_end', $rangeStart],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     * @return Order[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Order|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
