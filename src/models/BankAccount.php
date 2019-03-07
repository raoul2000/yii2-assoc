<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "bank_account".
 *
 * @property int $id
 * @property int $contact_id
 * @property string $contact_name copy of the contact name
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
            [['contact_id', 'name'], 'required'],
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
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::beforeDelete()
     */
    public function beforeDelete()
    {
        foreach ($this->fromTransactions as $transaction) {
            $transaction->delete();
        }        
        foreach ($this->toTransactions as $transaction) {
            $transaction->delete();
        }   
        return true;     
    }    

    /**
     * Initialize and save the 'contact_name' attribute with the name of the related contact.
     * 
     * @see \app\models\Contact::afterSave()
     * @see \yii\db\BaseActiveRecord::afterSave($insert, $changedAttributes)
     */
    public function afterSave($insert, $changedAttributes)
    {
        if($insert && empty($this->contact_name)) {
            $this->contact_name = $this->contact->name;
            $this->save(false);
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
     * Returns all transaction having this account as source (from)
     * @return \yii\db\ActiveQuery
     */
    public function getFromTransactions()
    {
        return $this->hasMany(Transaction::className(), ['from_account_id' => 'id']);
    }       
    /**
     * Returns all transaction having this account as target (to)
     * @return \yii\db\ActiveQuery
     */
    public function getToTransactions()
    {
        return $this->hasMany(Transaction::className(), ['to_account_id' => 'id']);
    }     


    /**
     * Returns an array containing all bank account names indexed by account id. 
     * Account names are prefixed with the contact name.
     */
    public static function getNameIndex() {
        $accounts = parent::find()
        ->asArray()
        ->with(['contact'])          
        ->all();
        
        return ArrayHelper::map($accounts, 'id', function($item) {
            return $item['contact']['name'] . ( empty($item['name'] ) 
                ? ''
                : ' (' . $item['name'] . ')');
        });
    }
}
