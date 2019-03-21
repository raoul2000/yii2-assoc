<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use bupy7\activerecord\history\behaviors\History as HistoryBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii2tech\ar\softdelete\SoftDeleteQueryBehavior;
use thamtech\uuid\helpers\UuidHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "contact".
 *
 * @property int $id
 * @property binary $uuid
 * @property string $name name
 * @property int $address_id
 * @property int $is_natural_person TRUE if this contact represent a natural person, FALSE if it represent a legal person
 * @property int $created_at timestamp of record creation (see TimestampBehavior)
 * @property int $updated_at timestamp of record last update (see TimestampBehavior)
 * @property bool $is_deleted used by soft delete behavior to flag a record as soft deleted
 */
class Contact extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact';
    }
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
             \app\components\behaviors\AttachmentBehavior::className(),
            TimestampBehavior::className(),
            [
                'class' => HistoryBehavior::className(),
                'skipAttributes' => [
                    'created_at',
                    'updated_at',
                ],
            ],
            // disable soft delete
            /*
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],*/
        ];
    }

    public static function find()
    {
        $query = parent::find();
        // Hard code soft delete condition because I could not use
        // the SoftDeleteQueryBehavior behavior described in https://github.com/yii2tech/ar-softdelete
        //    $query->attachBehavior('softDelete', SoftDeleteQueryBehavior::className());

        // disable soft delete
        //$query->andWhere([ 'is_deleted' => 0]);
        return $query;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_natural_person'], 'required'],
            [['is_natural_person'], 'boolean'],
            [['uuid'], 'default', 'value' => UuidHelper::uuid()],
            [['created_at', 'updated_at','is_deleted'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['address_id'], 'default','value' => null],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'UUID',
            'name' => 'name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted'
        ];
    }
    /**
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::beforeDelete()
     */
    public function beforeDelete()
    {
        foreach ($this->bankAccounts as $account) {
            $account->delete();
        }
        foreach ($this->orders as $order) {
            $order->delete();
        }
        return true;
    }

    /**
     * @see \app\models\BankAccount::afterSave()
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        // contact.name to all related account contact_name column
        if (!$insert && isset($changedAttributes['name'])) {
            foreach ($this->bankAccounts as $bankAccount) {
                $bankAccount->contact_name = $this->name;
                $bankAccount->save(false);
            }
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBankAccounts()
    {
        return $this->hasMany(BankAccount::className(), ['contact_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['contact_id' => 'id']);
    }

    /**
     * Returns an array containing all contact names indexed by contact Id.
     *
     * @returns array list of [id, name] items
     */
    public static function getNameIndex()
    {
        $contacts = parent::find()
            ->select(['id','name'])
            ->asArray()
            ->all();
        return ArrayHelper::map($contacts, 'id', 'name');
    }
}
