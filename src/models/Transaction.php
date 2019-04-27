<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use bupy7\activerecord\history\behaviors\History as HistoryBehavior;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property int $from_account_id
 * @property int $to_account_id
 * @property int $is_verified (boolean) is this transaction verified ?
 * @property string $value
 * @property date $reference_date
 * @property string $description
 * @property string $code a free value describing the transaction
 * @property int $transaction_pack_id Id of the pack that inlcudes this transaction or NULL
 * @property int $created_at timestamp of record creation (see TimestampBehavior)
 * @property int $updated_at timestamp of record last update (see TimestampBehavior)
 *
 * @property BankAccount $fromAccount
 * @property BankAccount $toAccount
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * Create a new instance of this model with default attributes values
     *
     * @return Transaction
     */
    public static function create()
    {
        return new Transaction([
            'reference_date' => date('Y-m-d'),
            'value' => 0
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            \app\components\behaviors\TimestampBehavior::className(),
            \app\components\behaviors\AttachmentBehavior::className(),
            [
                'class' => HistoryBehavior::className(),
                'skipAttributes' => [
                    'created_at',
                    'updated_at',
                ],
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_account_id', 'to_account_id','value'], 'required'],
            [['from_account_id', 'to_account_id', 'transaction_pack_id'], 'integer'],
            [['value'], 'number', 'min' => 0],

            [['description'], 'string', 'max' => 128],
            [['code'], 'string', 'max' => 10],
            /*
            // from and to account must not be the same, expect when transaction value is 0. This is a 
            // particular case used to cancel a bank check for example
            // This rule is specific and should not be part of the base class
            ['from_account_id', 'compare', 'compareAttribute' => 'to_account_id', 'operator' => '!=', 'type' => 'number',
                'when' => function ($model) {
                    return $model->value != 0;
                },
                'whenClient' => "function(attribute, value) {
                    return $('#transaction-value').val() != '0';
                }
            "],*/
            [['from_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => BankAccount::className(), 'targetAttribute' => ['from_account_id' => 'id']],
            [['to_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => BankAccount::className(), 'targetAttribute' => ['to_account_id' => 'id']],

            [['is_verified'], 'boolean'],

            [['reference_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_account_id' => 'From Account ID',
            'to_account_id' => 'To Account ID',
            'value' => 'Value',
            'is_verified' => 'Is Verified',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'reference_date' => 'Reference Date',
            'code' => 'Code',
            'transaction_pack_id' => 'Pack ID',
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        foreach ($this->orders as $order) {
            $this->unlink('orders', $order, true);
        }
        return true;
    }
    /**
     * Getter for the orderValueDiff virtual attribute.
     * This attribute represents the difference between the transaction's value and the sum of all its
     * related orders value.
     * If the value is negative, all the value of the transaction is dispatched among orders. This is a normal situation as
     * order's value can be covered by more than one transaction.
     * If the value is positive, it means that not all the value of this transaction is assigned to an order.
     * If the value is zero, all the value of the transaction covers value of order(s)
     *
     * @return int
     */
    public function getOrderValuesDiff()
    {
        if ($this->isNewRecord) {
            return null;
        }
        if (empty($this->orders)) {
            return null;
        } else {
            $sum = 0;
            foreach ($this->orders as $order) {
                $sum += $order->value;
            }
            return $this->value - $sum;
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromAccount()
    {
        return $this->hasOne(BankAccount::className(), ['id' => 'from_account_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToAccount()
    {
        return $this->hasOne(BankAccount::className(), ['id' => 'to_account_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderTransactions()
    {
        return $this->hasMany(OrderTransaction::className(), ['transaction_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this
            ->hasMany(Order::className(), ['id' => 'order_id'])
            ->via('orderTransactions');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPack()
    {
        return $this->hasOne(TransactionPack::className(), ['id' => 'transaction_pack_id']);
    }
}
