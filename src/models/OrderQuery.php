<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Order]].
 *
 * @see Order
 */
class OrderQuery extends \yii\db\ActiveQuery
{
    use \app\components\DateRangeQueryTrait;
    use \app\components\SmartDateConditionTrait;

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
