<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "address".
 *
 * @property int $id
 * @property string $line_1
 * @property string $line_2
 * @property string $line_3
 * @property string $zip_code
 * @property string $city
 * @property string $country
 * @property string $note
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Contact[] $contacts
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'address';
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
            [['created_at', 'updated_at'], 'integer'],
            [['line_1', 'line_2', 'line_3', 'note'], 'string', 'max' => 128],
            [['zip_code', 'city', 'country'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'line_1' => 'Line 1',
            'line_2' => 'Line 2',
            'line_3' => 'Line 3',
            'zip_code' => 'Zip Code',
            'city' => 'City',
            'country' => 'Country',
            'note' => 'Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::className(), ['address_id' => 'id']);
    }
    /**
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::beforeDelete()
     */
    public function beforeDelete()
    {
        foreach ($this->contacts as $contact) {
            $contact->updateAttributes([
                'address_id' => null
            ]);
        }
        return true;
    }    
}
