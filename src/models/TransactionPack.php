<?php

namespace app\models;

use Yii;

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
            //\yii\behaviors\TimestampBehavior::className(),
            /*
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'value' => new \yii\db\Expression('NOW()'),
            ],*/            
            \app\components\behaviors\TimestampBehavior::className(),
            //\yii\behaviors\BlameableBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reference_date'], 'safe'],
            [['name'], 'string', 'max' => 128],
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
}
