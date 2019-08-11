<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use bupy7\activerecord\history\behaviors\History as HistoryBehavior;

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
            'line_1' => \Yii::t('app', 'Line 1'),
            'line_2' => \Yii::t('app', 'Line 2'),
            'line_3' => \Yii::t('app', 'Line 3'),
            'zip_code' => \Yii::t('app', 'Zip Code'),
            'city' => \Yii::t('app', 'City'),
            'country' => \Yii::t('app', 'Country'),
            'note' => \Yii::t('app', 'Note'),
            'created_at' => \Yii::t('app', 'Created At'),
            'updated_at' => \Yii::t('app', 'Updated At'),
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
        if (!parent::beforeDelete()) {
            return false;
        }
        foreach ($this->contacts as $contact) {
            $contact->updateAttributes([
                'address_id' => null
            ]);
        }
        return true;
    }
}
