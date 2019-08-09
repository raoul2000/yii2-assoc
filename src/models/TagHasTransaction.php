<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tag_has_transaction".
 *
 * @property int $tag_id
 * @property int $transaction_id
 *
 * @property Tag $tag
 * @property Transaction $transaction
 */
class TagHasTransaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag_has_transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tag_id', 'transaction_id'], 'required'],
            [['tag_id', 'transaction_id'], 'integer'],
            [['tag_id', 'transaction_id'], 'unique', 'targetAttribute' => ['tag_id', 'transaction_id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::className(), 'targetAttribute' => ['tag_id' => 'id']],
            [['transaction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transaction::className(), 'targetAttribute' => ['transaction_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => Yii::t('app', 'Tag ID'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::className(), ['id' => 'tag_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransaction()
    {
        return $this->hasOne(Transaction::className(), ['id' => 'transaction_id']);
    }
}
