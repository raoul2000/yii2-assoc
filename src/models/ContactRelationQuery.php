<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Transaction]].
 *
 * @see Transaction
 */
class ContactRelationQuery extends \yii\db\ActiveQuery
{
    use \app\components\DateRangeQueryTrait;
    use \app\components\SmartDateConditionTrait;

    /**
     * {@inheritdoc}
     * @return Transaction[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Transaction|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
