<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use \app\components\ModelRegistry;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property int $contact_id
 * @property int $type
 * @property string $name
 *
 * @property Contact $contact
 */
class Category extends \yii\db\ActiveRecord
{
    const TRANSACTION = 'TR';
    const PRODUCT = 'PR';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name'], 'required'],
            [['contact_id'], 'integer'],
            [['name'], 'string', 'max' => 140],
            [['type'], 'string', 'max' => 128],
            ['type', function ($attribute, $params, $validator) {
                if (!array_key_exists($this->$attribute, Category::getTypes())) {
                    $this->addError($attribute, 'Invalid type');
                }
            }],
            [['contact_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Contact::className(),
                'targetAttribute' => ['contact_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contact_id' => \Yii::t('app', 'Contact ID'),
            'type' => \Yii::t('app', 'Type'),
            'name' => \Yii::t('app', 'Name'),
        ];
    }
    /**
     * Reset all records refering to this category
     *
     * @return void
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        switch ($this->type) {
            case ModelRegistry::TRANSACTION:
                Transaction::updateAll(
                    ['category_id' => null],
                    ['category_id' => $this->id]
                );
            break;
            case ModelRegistry::PRODUCT:
                Product::updateAll(
                    ['category_id' => null],
                    ['category_id' => $this->id]
                );
            break;
            default:
                throw new Exception('invalid category type : ' . $this->type);
        }
        return true;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
    }
    /**
     * Returns list of all category types
     *
     * @return array associative array where type id is the key and type name is the value
     */
    public static function getTypes()
    {
        return [
            ModelRegistry::TRANSACTION => ModelRegistry::getById(ModelRegistry::TRANSACTION)->label,
            ModelRegistry::PRODUCT     => ModelRegistry::getById(ModelRegistry::PRODUCT)->label,
        ];
    }
    /**
     * Returns the type name given the type Id
     *
     * @param string $typeId
     * @return string
     */
    public static function getTypeName($typeId)
    {
        $type = ModelRegistry::getById($typeId);
        if ($type === null) { 
            throw new Exception('type name not found for id ' . $typeId);
        }
        return $type->label;
    }
    /**
     * {@inheritdoc}
     * @return CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    public static function getCategories($type, $contact_id = null)
    {
        $categories = parent::find()
            ->select(['id','name'])
            ->where([
                'type' => $type,
                //'contact_id' => $contact_id   // categories are not private anymore
            ])
            ->asArray()
            ->all();
        return ArrayHelper::map($categories, 'id', 'name');
    }
}
