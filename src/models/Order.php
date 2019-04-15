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
 * @property int $contact_id
 * @property int $value
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
            \app\components\behaviors\TimestampBehavior::className(),
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
            [[ 'product_id', 'contact_id'], 'integer'],
            [['product_id', 'contact_id'], 'required'],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],

            [['initial_quantity'], 'number', 'min' => 1, 'integerOnly' => true],
            [['initial_quantity'], 'default', 'value' => 1],

            // Validity Date Range ///////////////////////////////////////////////////
            
            [['valid_date_start', 'valid_date_end'], 'date', 'format' => 'php:Y-m-d'],
            ['valid_date_start', 'compare',
                'when' => function ($model) {
                    return $model->valid_date_end != null;
                },
                'compareAttribute' => 'valid_date_end',
                'operator' => '<',
                'enableClientValidation' => false
            ],

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
            'valid_date_start' => 'Valid Date Start',
            'valid_date_end' => 'Valid Date End',
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
        if ($this->isNewRecord) {
            return null;
        }
        if (empty($this->transactions)) {
            return null;
        } else {
            $sum = 0;
            foreach ($this->transactions as $transaction) {
                $sum += $transaction->value;
            }
            return $sum - $this->value;
        }
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
