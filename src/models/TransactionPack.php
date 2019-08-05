<?php

namespace app\models;

use Yii;
use bupy7\activerecord\history\behaviors\History as HistoryBehavior;

/**
 * This is the model class for table "transaction_pack".
 *
 * @property int $id
 * @property string $name
 * @property string $reference_date
 * @property int $created_at
 * @property int $updated_at
 * @property int $bank_account_id
 * @property int $type
 *
 * @property Transaction[] $transactions
 */
class TransactionPack extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction_pack';
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
            [
                'class' => \app\components\behaviors\DateConverterBehavior::className(),
                'attributes' => [
                    'reference_date',
                ],
            ],
        ];
    }
    /**
     * {@inheritdoc}
     * @return TransactionPackQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TransactionPackQuery(get_called_class());
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 128],

            [['reference_date'], 'safe'],
            [['reference_date'], 'date',  'format' => Yii::$app->params['dateValidatorFormat']],
            
            [['bank_account_id', 'type'], 'integer'],
            [['bank_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => BankAccount::className(), 'targetAttribute' => ['bank_account_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'reference_date' => 'Reference Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'bank_account_id' => 'Bank Account ID',
            'type' => 'Type',
        ];
    }
    /**
     * {@inheritdoc}
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
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['transaction_pack_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBankAccount()
    {
        return $this->hasOne(BankAccount::className(), ['id' => 'bank_account_id']);
    }

    public function getValueSum()
    {
        return Transaction::find()
            ->where(['transaction_pack_id' => $this->id])
            ->sum('value');
    }
}
