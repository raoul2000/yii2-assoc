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

    public $order_count;
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
            [
                'class' => \app\components\behaviors\DateConverterBehavior::className(),
                'attributes' => [
                    'birthday',
                ],
            ],
            'taggable' => [
                'class' => \app\components\behaviors\TaggableBehavior::className(),
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
        // Hard code soft delete condition because I could not use
        // the SoftDeleteQueryBehavior behavior described in https://github.com/yii2tech/ar-softdelete
        //    $query->attachBehavior('softDelete', SoftDeleteQueryBehavior::className());

        // disable soft delete
        //$query->andWhere([ 'is_deleted' => 0]);

        return new ContactQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', "required"],
            [['is_natural_person'], 'required'],
            [['is_natural_person'], 'boolean'],
            [['uuid'], 'default', 'value' => UuidHelper::uuid()],
            [['is_deleted', 'gender'], 'integer'],
            ['gender','in', 'range' => [0,1,2] ],
            [['name', 'firstname', 'email'], 'string', 'max' => 128],
            [['note'], 'string', 'max' => 255],

            [['phone_1', 'phone_2'], 'string', 'max' => 50],
            ['email', 'email'],

            [['birthday', 'date_1'], 'date', 'format' => Yii::$app->params['dateValidatorFormat']],
            // tags
            ['tagValues', 'safe'],
        ];
    }
    /**
     * Filter attributes to return by the REST API
     *
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
    
        // remove fields that contain sensitive information
        unset($fields['address_id'], $fields['uuid'], $fields['is_natural_person'], $fields['is_deleted']);
        $fields['fullname'] = function($model) {
            return $model->getFullname();
        };
        return $fields;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'UUID',
            'name' => ($this->is_natural_person == 0 ? \Yii::t('app', 'Raison Sociale') : \Yii::t('app', 'Name')),
            'firstname' => ($this->is_natural_person == 0 ? \Yii::t('app', 'Complément') : \Yii::t('app', 'Firstname')),
            'created_at' => \Yii::t('app', 'Created At'),
            'updated_at' => \Yii::t('app', 'Updated At'),
            'is_deleted' => \Yii::t('app', 'Is Deleted'),
            'is_natural_person' => \Yii::t('app', 'Is Natural Person'),
            'birthday' => \Yii::t('app', 'Birthday'),
            'gender' => \Yii::t('app', 'Gender'),
            'email' => \Yii::t('app', 'Email'),
            'note' => \Yii::t('app', 'Note'),
            'phone_1' => \Yii::t('app', 'Phone 1'),
            'phone_2' => \Yii::t('app', 'Phone 2'),
            'date_1' => \Yii::t('app', 'Date 1'),
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
        // delete owned bank account(s)
        foreach ($this->bankAccounts as $account) {
            $account->delete();
        }

        // Deletes to and from orders
        foreach ($this->toOrders as $order) {
            $order->delete();
        }
        foreach ($this->fromOrders as $order) {
            $order->delete();
        }

        // deletes owned categorie(s)
        foreach ($this->categories as $category) {
            $category->delete();
        }

        // delete relation(s) with other contact(s) no matter
        // if they are to or from relations
        foreach ($this->relatedToContacts as $contact) {
            $this->unlink('relatedToContacts', $contact, true);
        }
        foreach ($this->relatedFromContacts as $contact) {
            $this->unlink('relatedFromContacts', $contact, true);
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
    /**
     * Returns TRUE if this contact is related to an address, FALSE otherwise
     *
     * @return boolean
     */
    public function getHasAddress()
    {
        return $this->address_id !== null;
    }
    /**
     * Returns the address belonging to this contact
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }
    /**
     * Returns all bank account belonging to this contact
     * @return \yii\db\ActiveQuery
     */
    public function getBankAccounts()
    {
        return $this->hasMany(BankAccount::className(), ['contact_id' => 'id']);
    }
    /**
     * Returns all orders received by this this contact.
     * @return \yii\db\ActiveQuery
     */
    public function getToOrders()
    {
        return $this->hasMany(Order::className(), ['to_contact_id' => 'id']);
    }
    /**
     * Returns all orders provided by this contact
     * @return \yii\db\ActiveQuery
     */
    public function getFromOrders()
    {
        return $this->hasMany(Order::className(), ['from_contact_id' => 'id']);
    }
    /**
     * Returns all categories belonging to this contact
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['contact_id' => 'id']);
    }
    /**
     * Returns Conatcts related to this contact where this contact is the source
     * of the relation.
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedToContacts()
    {
        return $this->hasMany(ContactRelation::className(), ['source_contact_id' => 'id']);
    }
    /**
     * Returns Conatcts related to this contact where this contact is the target
     * of the relation.
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedFromContacts()
    {
        return $this->hasMany(ContactRelation::className(), ['target_contact_id' => 'id']);
    }
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
            ->viaTable('{{%tag_has_contact}}', ['contact_id' => 'id']);
    }    
    /**
     * Returns an array containing all contact long names indexed by contact Id.
     *
     * @returns array list of [id, longname] items
     */
    public static function getNameIndex()
    {
        $contacts = parent::find()
            ->select(['id', 'name', 'firstname'])
            ->asArray()
            ->all();

        // sql:CONCAT cannot be used in the query because when firstnale is NULL, it returns NULL
        // so we build the long name below

        $contacts = array_map(function($row){
            $row['longName'] = $row['name'] . ( empty($row['firstname']) ? '' : ' ' . $row['firstname']);
            return $row;
        },$contacts);

        return ArrayHelper::map($contacts, 'id', 'longName');
    }
    /**
     * Returns the read-only attribute 'longName'
     *
     * @return string
     */
    public function getLongName()
    {
        if ($this->is_natural_person == true) {
            return $this->name . ( !empty($this->firstname) ? ' ' . $this->firstname : '');
        } else {
            return $this->name;
        }
    }
    /**
     * Synonym for getLongName
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->getLongName();
    }

}
