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
 * @property int $initial_value initial value of the account
 * @property int $created_at timestamp of record creation (see TimestampBehavior)
 * @property int $updated_at timestamp of record last update (see TimestampBehavior)
 * @property Contact $contact
 */
class BankAccount extends \yii\db\ActiveRecord
{
    const DEFAULT_NAME = 'principal';
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
            [['created_at', 'updated_at', 'contact_id'], 'integer'],
            [['name'], 'default', 'value' => BankAccount::DEFAULT_NAME],
            [['name'], 'string', 'max' => 45],
            ['name', 'unique', 'targetAttribute' => ['contact_id', 'name'], 'message' => 'Account name already used'],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],

            [['initial_value'], 'default', 'value' => 0],
            [['initial_value'], 'number'],
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
            'initial_value' => 'Initial Value',
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
     * Maintain synchro between the related contact record, and the 'contact_name' column.
     *
     * @see \app\models\Contact::afterSave()
     * @see \yii\db\BaseActiveRecord::afterSave($insert, $changedAttributes)
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert || (!$insert && isset($changedAttributes['contact_id']))) {
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
    public static function getNameIndex()
    {
        $accounts = parent::find()
            ->asArray()
            ->with(['contact'])
            ->all();
        
        return ArrayHelper::map($accounts, 'id', function ($item) {
            return $item['contact']['name'] . ( empty($item['name'])
                ? ''
                : ' (' . $item['name'] . ')');
        });
    }

    public function getLongName()
    {
        return $this->contact_name . ( empty($this->name) ? '' : ' - ' . $this->name);
    }
}
