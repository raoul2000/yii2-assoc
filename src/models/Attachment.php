<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

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
            TimestampBehavior::className(),
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
            'name' => 'Name',
            'model' => 'Model',
            'category_id' => 'Category ID',
            'note' => 'Notes',
            'hash' => 'Hash',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'itemId' => 'Item ID',
            'size' => 'Size',
            'type' => 'Type',
            'mime' => 'Mime',
        ];
    }
}
