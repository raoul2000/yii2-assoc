<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_transaction".
 *
 * @property int $order_id
 * @property int $transaction_id
 *
 * @property Order $order
 * @property Transaction $transaction
 */
class OrderTransaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_transaction';
    }
    // TODO: decide if we should store the sum of transaction values in order and
    // the sum of order values in transaction ? 
    /*
    public function init()
    {
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'onAfterInsert']);
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'onAfterDelete']);
        parent::init();
    }

    public function onAfterInsert($event)
    {
        $this->order->trigger(Order::EVENT_REFRESH_COVER_VALUE_DIFF);
        $this->transaction->trigger(Transaction::EVENT_REFRESH_COVER_VALUE_DIFF);
    }*/
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'transaction_id'], 'required'],
            [['order_id', 'transaction_id'], 'integer'],
            [['order_id', 'transaction_id'], 'unique', 'targetAttribute' => ['order_id', 'transaction_id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['transaction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transaction::className(), 'targetAttribute' => ['transaction_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'transaction_id' => 'Transaction ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransaction()
    {
        return $this->hasOne(Transaction::className(), ['id' => 'transaction_id']);
    }
}
