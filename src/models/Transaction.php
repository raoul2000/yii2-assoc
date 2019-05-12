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
 * @property float $value
 * @property float $orders_value_total computed value representing the sum of related transactions values
 * @property date $reference_date
 * @property string $description
 * @property string $code a free value describing the transaction
 * @property string $type
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
     * @return TransactionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TransactionQuery(get_called_class());
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
     * Link this transaction with an Order Model and updates the orders value total
     *
     * @param [Order] $orderModel
     * @return void
     */
    public function linkToOrder($orderModel)
    {
        $this->link('orders', $orderModel);
        $this->updateOrdersValueTotal();
        $orderModel->updateTransactionsValueTotal();
    }
    /**
     * Unlink this transaction from an ORder instance and updates the order value total column
     */
    public function unlinkFromOrder($orderModel)
    {
        $this->unlink('orders', $orderModel, true);
        $this->updateOrdersValueTotal();
        $orderModel->updateTransactionsValueTotal();
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
            [['code', 'type'], 'string', 'max' => 10],
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
            [['transaction_pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => TransactionPack::className(), 'targetAttribute' => ['transaction_pack_id' => 'id']],

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
            'type' => 'Type',
        ];
    }

    public function afterSave( $insert, $changedAttributes)
    {
        if (!$insert) { // update
            // if value has changed, update the 'transactions_value_total' column for
            // all related orders
            if (array_key_exists('value', $changedAttributes)) {
                foreach ($this->orders as $order) {
                    $order->updateTransactionsValueTotal();
                }                
            }
        }
    }    
    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        foreach ($this->orders as $order) {
            $this->unlinkFromOrder($order);
        }
        return true;
    }

    /**
     * Update the total orders values paid by this transaction.
     * This method modifies the transaction row by updating the 'orders_value_total' column
     * with a new value
     *
     * @return void
     */
    public function updateOrdersValueTotal()
    {
        if (!$this->isNewRecord) {
            $this->updateAttributes([
                'orders_value_total' => $this->sumOrdersValue()
            ]);
        }
    }

    public function sumOrdersValue()
    {
        if ($this->isNewRecord || empty($this->orders)) {
            return null;
        } else {
            $sum = 0;
            foreach ($this->orders as $order) {
                $sum += $order->value;
            }
            return round($sum, 2);
            /**
             * Alternative : 
             * $total = array_reduce($this->orders, function($acc, $order) {
             *     return $acc + $order->value;
             * },0);
             * return round($total , 2);
             */
        }        
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
        return ($this->orders_value_total === null ? null : round($this->value - $this->orders_value_total));
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
        /*
        return $this
            ->hasMany(Order::className(), ['id' => 'order_id'])
            ->viaTable('order_transaction', ['transaction_id' => 'id']);
        */
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
