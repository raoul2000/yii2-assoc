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
 * @property string $firstname
 * @property int $address_id
 * @property int $is_natural_person TRUE if this contact represent a natural person, FALSE if it represent a legal person
 * @property int $created_at timestamp of record creation (see TimestampBehavior)
 * @property int $updated_at timestamp of record last update (see TimestampBehavior)
 * @property bool $is_deleted used by soft delete behavior to flag a record as soft deleted
 * @property date $birthday
 * @property int $gender 1 = Male 2 = female
 * @property string $email
 * @property string $note
 * @property string $phone_1
 * @property string $phone_2
 * @property date $date_1 free to use date 
 */
class Contact extends \yii\db\ActiveRecord
{
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact';
    }
    /**
     * Create a new instance of this model with default attributes values
     *
     * @return Contact
     */
    public static function create()
    {
        return new Contact([
            'gender' => 0,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
             \app\components\behaviors\AttachmentBehavior::className(),
             \app\components\behaviors\TimestampBehavior::className(),
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
            [['is_deleted', 'gender'], 'integer'],
            ['gender','in', 'range' => [0,1,2] ],
            [['name', 'firstname', 'email', 'note'], 'string', 'max' => 128],
            [['phone_1', 'phone_2'], 'string', 'max' => 15],
            ['email', 'email'],
            [['birthday', 'date_1'], 'date', 'format' => 'php:Y-m-d'],
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
            'name' => ($this->is_natural_person == 0 ? 'Raison Sociale' : 'Name'),
            'firstname' => ($this->is_natural_person == 0 ? 'Complément' : 'Firstname'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'is_natural_person' => 'Is Natural Person',
            'birthday' => 'Birthday',
            'gender' => 'Gender',
            'email' => 'Email',
            'note' => 'Note',
            'phone_1' => 'Phone 1',
            'phone_2' => 'Phone 2',
            'date_1' => 'Date 1',
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
        foreach ($this->toOrders as $order) {
            $order->delete();
        }

        foreach ($this->fromOrders as $order) {
            $order->delete();
        }

        foreach ($this->categories as $category) {
            $category->delete();
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
        if (
            !$insert 
            && (isset($changedAttributes['name']) || isset($changedAttributes['firstname']))
        ) {
            foreach ($this->bankAccounts as $bankAccount) {
                $bankAccount->contact_name = $this->getLongName();
                $bankAccount->save(false);
            }
        }
    }
    public function getHasAddress()
    {
        return $this->address_id !== null;
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
    public function getToOrders()
    {
        return $this->hasMany(Order::className(), ['to_contact_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromOrders()
    {
        return $this->hasMany(Order::className(), ['from_contact_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['contact_id' => 'id']);
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

    public function getLongName()
    {
        if ($this->is_natural_person == true) {
            return $this->name . ( !empty($this->firstname) ? ', ' . $this->firstname : '');
        } else {
            return $this->name;
        }

    }
}
