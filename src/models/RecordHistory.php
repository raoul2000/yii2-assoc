<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "arhistory".
 *
 * @property int $id
 * @property string $table_name
 * @property int $row_id
 * @property int $event
 * @property int $created_at
 * @property int $created_by
 * @property string $field_name
 * @property string $old_value
 * @property string $new_value
 */
class RecordHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'arhistory';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => 'Table Name',
            'row_id' => 'Row ID',
            'event' => 'Event',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'field_name' => 'Field Name',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
        ];
    }
}
