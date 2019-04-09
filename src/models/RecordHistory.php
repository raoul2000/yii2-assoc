<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

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
    const ROUTES = [
        'contact' => 'contact/view'
    ];
    const TABLE_MAP = [
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

    public function getRecordViewUrl()
    {
        /*
        if (!\array_key_exists($this->table_name, RecordHistory::ROUTES)) {
            return null;
        }
        return Url::toRoute([RecordHistory::ROUTES[$this->table_name], 'id' => $this->row_id]);
        */
        return Url::toRoute([$this->table_name . '/view', 'id' => $this->row_id]);
    }

    public static function getRecordHistoryIndex($model)
    {
        //http://localhost/dev/ws/lab/yii2-assoc/src/web/index.php?
        //  RecordHistorySearch%5Btable_name%5D=transaction&
        //  RecordHistorySearch%5Brow_id%5D=4&
        //
        $tableName = $model->getTableName();
        $id = $moçdel->id;

        return Url::toRoute([
            'record-history/index',
            'RecordHistorySearch[table_name]' => $tableName,
            'RecordHistorySearch[5Brow_id]' => $id
        ]);
    }
}
