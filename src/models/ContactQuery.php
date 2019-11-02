<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Transaction]].
 *
 * @see Transaction
 */
class ContactQuery extends \yii\db\ActiveQuery
{
    use \app\components\SmartDateConditionTrait;

    public function behaviors()
    {
        return [
            \app\components\behaviors\TaggableQueryBehavior::className(),
        ];
    }
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
