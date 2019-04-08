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
    const EVENT_LABELS = [
        '1' => 'insert',
        '2' => 'update',
        '3' => 'delete'
    ];
    const TABLE_NAMES = [
        'contact' => 'contact',
        'transaction' => 'transaction',
        'order' => 'order',
        'address' => 'address',
        'attachment' => 'attachment',
        'bank_account' => 'bank_account',
        'product' => 'product',
        'transaction_pack' => 'transaction_pack',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'arhistory';
    }

    public static function getEventName($idx = null)
    {
        return $idx != null
            ? RecordHistory::EVENT_LABELS[$idx]
            : RecordHistory::EVENT_LABELS;
    }
    public static function getTableName($idx = null)
    {
        return $idx != null
            ? (array_key_exists($idx, RecordHistory::TABLE_NAMES) ? RecordHistory::TABLE_NAMES[$idx] : $idx)
            : RecordHistory::TABLE_NAMES;
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
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\Da\User\Model\User::className(), ['id' => 'created_by']);
    }
}
