<?php

namespace app\models;

use Yii;
use bupy7\activerecord\history\behaviors\History as HistoryBehavior;

/**
 * This is the model class for table "contact_has_contact".
 *
 * @property int $id
 * @property int $source_contact_id
 * @property int $target_contact_id
 * @property int $type
 * @property string $created_at
 * @property string $updated_at
 * @property string $valid_date_start
 * @property string $valid_date_end
 *
 * @property Contact $sourceContact
 * @property Contact $targetContact
 */
class ContactRelation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact_has_contact';
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
            [['source_contact_id', 'target_contact_id'], 'required'],
            [['source_contact_id', 'target_contact_id', 'type'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['source_contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['source_contact_id' => 'id']],
            [['target_contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['target_contact_id' => 'id']],


            // Validity Date Range ///////////////////////////////////////////////////
            
            [['valid_date_start', 'valid_date_end'], 'date', 'format' => 'php:Y-m-d'],
            ['valid_date_start', 'compare',
                'when' => function ($model) {
                    return $model->valid_date_end != null;
                },
                'compareAttribute' => 'valid_date_end',
                'operator' => '<=',
                'enableClientValidation' => false
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
            'source_contact_id' => 'Source Contact ID',
            'target_contact_id' => 'Target Contact ID',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'valid_date_start' => 'Valid Date Start',
            'valid_date_end' => 'Valid Date End',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSourceContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'source_contact_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargetContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'target_contact_id']);
    }

}
