<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

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
    /**
     * Match between table name (key) and table display name (value)
     */
    const TABLE_NAMES = [
        'contact' => 'contact',
        'transaction' => 'transaction',
        'order' => 'order',
        'address' => 'address',
        'attachment' => 'attachment',
        'bank_account' => 'bank account',
        'product' => 'product',
        'transaction_pack' => 'transaction pack',
    ];
    const ROUTES = [ // not used
        'contact' => 'contact/view'
    ];
    const TABLE_MAP = [ // no used
        'contact' => [
            'label' => 'Contact',
            'viewRoute' => 'contact/view'
        ],
        'transaction' => [
            'label' => 'transaction',
            'viewRoute' => 'transaction/view'
        ],
        'order' => [
            'label' => 'order',
            'viewRoute' => 'order/view'
        ],
        'address' => [
            'label' => 'address',
            'viewRoute' => 'address/view'
        ],
        'attachment' => [
            'label' => 'attachment',
            'viewRoute' => 'attachment/view'
        ],
        'bank_account' => [
            'label' => 'bank account',
            'viewRoute' => 'bank_account/view'
        ],
        'product' => [
            'label' => 'product',
            'viewRoute' => 'product/view'
        ],
        'transaction_pack' => [
            'label' => 'transaction_pack',
            'viewRoute' => 'transaction_pack/view'
        ],
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
    /**
     * Returns the map of all users having a row in the session table.
     * The list returned is a user_id,username map, suitable to be used to initialize
     * select box
     *
     * @return void
     */
    public static function getUsernameIndex()
    {
        $userIds = parent::find()
            ->select('created_by')
            ->distinct()
            ->with(['user'])
            ->asArray()
            ->all();

        return ArrayHelper::map($userIds, 'created_by', function ($item) {
            return $item['user']['username'];
        });
    }
    /**
     * Creates and returns the URL of the record described by this history item
     * The URL returned is the one of the model view page.
     * @return string
     */
    public function getRecordViewUrl()
    {
        return Url::toRoute([$this->table_name . '/view', 'id' => $this->row_id]);
    }

    /**
     * Returns an URL to the history index view for a record given its id and table name
     *
     * @param string $tableName
     * @param int $id
     * @return string the URL
     */
    public static function getRecordHistoryIndex($tableName, $id)
    {
        return Url::toRoute([
            'record-history/index',
            'RecordHistorySearch[table_name]' => $tableName,
            'RecordHistorySearch[row_id]' => $id
        ]);
    }
}
