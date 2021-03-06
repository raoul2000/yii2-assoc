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
            [
                'class' => \app\components\behaviors\DateConverterBehavior::className(),
                'attributes' => [
                    'valid_date_start',
                    'valid_date_end'
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
            
            [['valid_date_start', 'valid_date_end'], 'date', 'format' => Yii::$app->params['dateValidatorFormat']],
            ['valid_date_start', \app\components\validators\DateRangeValidator::className()],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'source_contact_id' => \Yii::t('app', 'Source Contact ID'),
            'target_contact_id' => \Yii::t('app', 'Target Contact ID'),
            'type' => \Yii::t('app', 'Type'),
            'created_at' => \Yii::t('app', 'Created At'),
            'updated_at' => \Yii::t('app', 'Updated At'),
            'valid_date_start' => \Yii::t('app', 'Valid Date Start'),
            'valid_date_end' => \Yii::t('app', 'Valid Date End'),
        ];
    }

    public static function find()
    {
        return new ContactRelationQuery(get_called_class());
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
