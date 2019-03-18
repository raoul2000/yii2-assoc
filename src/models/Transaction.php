<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property int $from_account_id
 * @property int $to_account_id
 * @property int $is_verified (boolean) is this transaction verified ?
 * @property string $value
 * @property string $description
 * @property int $created_at timestamp of record creation (see TimestampBehavior)
 * @property int $updated_at timestamp of record last update (see TimestampBehavior)
 *
 * @property BankAccount $fromAccount
 * @property BankAccount $toAccount
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * This attribute is used when creating a new transaction. It is used to optionnally store a product ID
     * that will be used to automatically created an Order record.
     *
     * @var int
     */
    public $initial_product_id;
    /**
     * Used when creating a transaction. Quantity of orders to created for the initial product ID selected
     *
     * @var int
     */
    public $initial_product_quantity;

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
            TimestampBehavior::className(),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_account_id', 'to_account_id','value'], 'required'],
            [['from_account_id', 'to_account_id', 'created_at', 'updated_at'], 'integer'],
            [['value'], 'number', 'min' => 0],

            [['initial_product_quantity'], 'number', 'min' => 1, 'integerOnly' => true],
            [['initial_product_quantity'], 'default', 'value' => 1],
            
            [['description'], 'string', 'max' => 128],
            ['from_account_id', 'compare', 'compareAttribute' => 'to_account_id', 'operator' => '!=', 'type' => 'number'],
            [['from_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => BankAccount::className(), 'targetAttribute' => ['from_account_id' => 'id']],
            [['to_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => BankAccount::className(), 'targetAttribute' => ['to_account_id' => 'id']],

            [['initial_product_id'], 'integer'],
            [['is_verified'], 'boolean'],
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
        ];
    }
    public function beforeDelete()
    {
        foreach ($this->orders as $order) {
            $this->unlink('orders',$order, true);
        }
        return true;
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
            ->viaTable('order_transaction', ['transaction_id' => 'id']);
    }        
}
