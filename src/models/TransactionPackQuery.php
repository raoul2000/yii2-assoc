<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TransactionPack]].
 *
 * @see TransactionPack
 */
class TransactionPackQuery extends \yii\db\ActiveQuery
{
    use \app\components\DateRangeQueryTrait;

    /**
     * {@inheritdoc}
     * @return TransactionPack[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TransactionPack|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
