<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "bank_account".
 *
 * @property int $id
 * @property int $contact_id
 * @property string $name
 * @property int $created_at timestamp of record creation (see TimestampBehavior)
 * @property int $updated_at timestamp of record last update (see TimestampBehavior)
 * @property Contact $contact
 */
class BankAccount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank_account';
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
            [['contact_id'], 'required'],
            [['contact_id'], 'integer'],
            [['created_at', 'updated_at'], 'integer'],            
            [['name'], 'string', 'max' => 45],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contact_id' => 'Contact ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',     
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
    }
    /**
     * Returns all transaction having this account as source (from)
     * @return \yii\db\ActiveQuery
     */
    public function getFromTransactions()
    {
        return $this->hasMany(Transaction::className(), ['from_contact_id' => 'id']);
    }       
    /**
     * Returns all transaction having this account as target (to)
     * @return \yii\db\ActiveQuery
     */
    public function getToTransactions()
    {
        return $this->hasMany(Transaction::className(), ['to_contact_id' => 'id']);
    }       
}
