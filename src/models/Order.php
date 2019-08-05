<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use bupy7\activerecord\history\behaviors\History as HistoryBehavior;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $product_id
 * @property int $to_contact_id
 * @property int $from_contact_id
 * @property float $value
 * @property float $transactions_value_total computed value representing the sum of all related order's value
 * @property int $created_at timestamp of record creation (see TimestampBehavior)
 * @property int $updated_at timestamp of record last update (see TimestampBehavior)
 * @property string $valid_date_start
 * @property string $valid_date_end
 *
 * @property Contact $contact
 * @property Product $product
 * @property Transaction $transaction
 */
class Order extends \yii\db\ActiveRecord
{
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
            \app\components\behaviors\TimestampBehavior::className(),
            [
                'class' => HistoryBehavior::className(),
                'skipAttributes' => [
                    'created_at',
                    'updated_at',
                ],
            ],
            [
                'class' => \app\components\behaviors\DateConverterBehavior::className(),
                'attributes' => [
                    'valid_date_start',
                    'valid_date_end'
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
            [[ 'product_id', 'to_contact_id'], 'integer'],
            [['product_id', 'to_contact_id', 'from_contact_id'], 'required'],
            [['to_contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['to_contact_id' => 'id']],
            [['from_contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['from_contact_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],

            // Validity Date Range ///////////////////////////////////////////////////
            
            [['valid_date_start', 'valid_date_end'], 'date', 'format' => Yii::$app->params['dateValidatorFormat']],
            ['valid_date_start', \app\components\validators\DateRangeValidator::className()],

            // Value
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
            'to_contact_id' => 'Destinataire',
            'from_contact_id' => 'Fournisseur',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'valid_date_start' => 'Valid Date Start',
            'valid_date_end' => 'Valid Date End',
        ];
    }

    /**
     * {@inheritdoc}
     * @return OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderQuery(get_called_class());
    }
        
    /**
     * Delete all links to transactions
     */
    public function beforeDelete()
    {
        foreach ($this->transactions as $transaction) {
            $this->unlinkFromTransaction($transaction);
        }
        return true;
    }

    /**
     * Link this Order with atransaction Model and updates the orders value total
     *
     * @param [Order] $orderModel
     * @return void
     */
    public function linkToTransaction($transactionModel)
    {
        $this->link('transactions', $transactionModel);
        $this->updateTransactionsValueTotal();
        $transactionModel->updateOrdersValueTotal();
    }
    /**
     * Unlink this transaction from an ORder instance and updates the order value total column
     */
    public function unlinkFromTransaction($transactionModel)
    {
        $this->unlink('transactions', $transactionModel, true);
        $this->updateTransactionsValueTotal();
        $transactionModel->updateOrdersValueTotal();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert) { // update
            // if value has changed, update 'orders_value_total' column on 
            // all the related transactions
            if (array_key_exists('value', $changedAttributes)) {
                foreach ($this->transactions as $transaction) {
                    $transaction->updateOrdersValueTotal();
                }
            }
        }
    }
    public function updateTransactionsValueTotal()
    {
        if (!$this->isNewRecord) {
            $this->updateAttributes([
                'transactions_value_total' => $this->sumTransactionsValue()
            ]);
        }
    }
    /**
     * Sum the value of all transactions related to this order. The returned value
     * is rounded to 2 decimals. If this order has no transaction or is not yet saved in the DB,
     * this method returns NULL.
     *
     * @return null|number
     */
    public function sumTransactionsValue()
    {
        if ($this->isNewRecord || empty($this->transactions)) {
            return null;
        } else {
            $sum = 0;
            foreach ($this->transactions as $transaction) {
                $sum += $transaction->value;
            }
            return round($sum, 2);
        }
    }
    /**
     * Getter for the transactionValueDiff virtual attribute.
     * This attribute represent the difference between the complete values provided by all related transactions, and the value of this order.
     * If the value is negative, we may have a problem : all related transaction's values are not covering the value of this order.
     * If the value is positive, it's ok : all transaction values are covering more than the value of this order which is possible because a transaction
     * can cover more than one order.
     * If the value is zero, the value of this order is fully covered by related transactions
     *
     * @return int
     */
    public function getTransactionValuesDiff()
    {
        return ($this->transactions_value_total === null ? null : round($this->transactions_value_total - $this->value));
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'to_contact_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'from_contact_id']);
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
