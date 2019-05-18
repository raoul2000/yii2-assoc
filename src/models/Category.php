<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

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
            [['contact_id', 'type', 'name'], 'required'],
            [['contact_id'], 'integer'],
            [['name'], 'string', 'max' => 140],
            [['type'], 'string', 'max' => 7],
            ['type', function($attribute, $params, $validator) {
                if (!array_key_exists( $this->$attribute, Category::getTypes())) {
                    $this->addError($attribute, 'Invalid type');
                }
            }],
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
            'type' => 'Type',
            'name' => 'Name',
        ];
    }
    /**
     * Reset all records refering to this category
     *
     * @return void
     */
    public function beforeDelete()
    {
        switch($this->type) {
            case self::TRANSACTION:
                Transaction::updateAll(
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
    static public function getTypes() {
        return [
            self::TRANSACTION => 'transaction'
        ];
    }
    static public function getTypeName($typeId) {
        $types = self::getTypes();
        return $types[$typeId];
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
            ->andFilterWhere([
                'type' => $type,
                'contact_id' => $contact_id
            ])
            ->asArray()
            ->all();
        return ArrayHelper::map($categories, 'id', 'name');
    }    
}
