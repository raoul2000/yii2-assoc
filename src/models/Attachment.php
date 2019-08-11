<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use bupy7\activerecord\history\behaviors\History as HistoryBehavior;

/**
 * This is the model class for table "attachment".
 *
 * @property int $id
 * @property string $name
 * @property string $model
 * @property int $category_id
 * @property string $note
 * @property string $hash
 * @property int $created_at
 * @property int $updated_at
 * @property int $itemId
 * @property int $size
 * @property string $type
 * @property string $mime
 */
class Attachment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attachment';
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
            [['name'], 'required'],
            [['note'], 'string', 'max' => 128],
            [['category_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => \Yii::t('app', 'Name'),
            'model' => \Yii::t('app', 'Model'),
            'category_id' => \Yii::t('app', 'Category ID'),
            'note' => \Yii::t('app', 'Notes'),
            'hash' => \Yii::t('app', 'Hash'),
            'created_at' => \Yii::t('app', 'Created At'),
            'updated_at' => \Yii::t('app', 'Updated At'),
            'itemId' => \Yii::t('app', 'Item ID'),
            'size' => \Yii::t('app', 'Size'),
            'type' => \Yii::t('app', 'Type'),
            'mime' => \Yii::t('app', 'Mime'),
        ];
    }
}
