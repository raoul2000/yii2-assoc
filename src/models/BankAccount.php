<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use bupy7\activerecord\history\behaviors\History as HistoryBehavior;

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
     * holds the latest balance info data for this bank account
     * @var array|null
     */
    private $_balanceInfo = null;

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
            [['contact_id'], 'required'],
            [['contact_id'], 'integer'],
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
        if (!parent::beforeDelete()) {
            return false;
        }
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
        parent::afterSave($insert, $changedAttributes);
        if ($insert || (!$insert && isset($changedAttributes['contact_id']))) {
            $this->contact_name = $this->contact->getLongName();
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
     * Returns a query to find all transactions for this order, no matter if they are
     * incoming or outgoing transactions
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return Transaction::find()
            ->where(['from_account_id' => $this->id])
            ->orWhere(['to_account_id' => $this->id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionPacks()
    {
        return $this->hasMany(TransactionPack::className(), ['bank_account_id' => 'id']);
    }
    /**
     * Compute and returns the current account balance and total Deb/Cred
     *
     * @param bool $refresh
     * @return array
     */
    public function getBalanceInfo($refresh = true)
    {
        if ($this->_balanceInfo == null || $refresh === true) {
            $totalDeb = Transaction::find()
                ->where(['from_account_id' => $this->id])
                ->sum('value');
    
            $totalCred = Transaction::find()
                ->where(['to_account_id' => $this->id])
                ->sum('value');
    
            $this->_balanceInfo = [
                'value' => $this->initial_value + $totalCred - $totalDeb,
                'totalDeb' => $totalDeb ? $totalDeb : 0,
                'totalCred' => $totalCred ? $totalCred : 0,
            ];
        }

        return $this->_balanceInfo;
    }
    /**
     * Returns an array containing all bank account names indexed by account id.
     * Account names are prefixed with the contact name.
     */
    public static function getNameIndex()
    {
        $accounts = parent::find()
            ->asArray()
            ->all();
        
        return ArrayHelper::map($accounts, 'id', function ($item) {
            return $item['contact_name'] . ( empty($item['name'])
                ? ''
                : ' (' . $item['name'] . ')');
        });
    }

    public function getLongName()
    {
        return $this->contact_name . ( empty($this->name) ? '' : ' - ' . $this->name);
    }
}
