<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $product_id
 * @property int $contact_id
 * @property int $value
 * @property int $created_at timestamp of record creation (see TimestampBehavior)
 * @property int $updated_at timestamp of record last update (see TimestampBehavior)
 *
 * @property Contact $contact
 * @property Product $product
 * @property Transaction $transaction
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * Allow creation of multiple orders in a row
     *
     * @var int
     */
    public $initial_quantity;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
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
            [[ 'product_id', 'contact_id', 'created_at', 'updated_at'], 'integer'],
            [['product_id', 'contact_id'], 'required'],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],

            [['initial_quantity'], 'number', 'min' => 1, 'integerOnly' => true],
            [['initial_quantity'], 'default', 'value' => 1],

            [['value'], 'default', 'value' => 0],
            [['value'], 'number', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
            'product_id' => 'Product ID',
            'contact_id' => 'Contact ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    /**
     * Delete all links to transactions
     */
    public function beforeDelete()
    {
        foreach ($this->transactions as $transaction) {
            $this->unlink('transactions', $transaction, true);
        }
        return true;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this
            ->hasMany(Transaction::className(), ['id' => 'transaction_id'])
            ->via('orderTransactions');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderTransactions()
    {
        return $this
            ->hasMany(OrderTransaction::className(), ['order_id' => 'id']);
    }
}
