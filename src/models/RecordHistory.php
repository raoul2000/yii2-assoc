<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use \app\components\ModelRegistry;

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
    public static function getTableNameIndex()
    {
        return ModelRegistry::getTableNameIndex();
    }
    public static function getTableName($value)
    {
        $model = ModelRegistry::getByTableName($value);
        if ($model != null) {
            return $model->label;
        } else {
            return null;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'table_name' => \Yii::t('app', 'Table Name'),
            'row_id' => \Yii::t('app', 'Row ID'),
            'event' => \Yii::t('app', 'Event'),
            'created_at' => \Yii::t('app', 'Created At'),
            'created_by' => \Yii::t('app', 'Created By'),
            'field_name' => \Yii::t('app', 'Field Name'),
            'old_value' => \Yii::t('app', 'Old Value'),
            'new_value' => \Yii::t('app', 'New Value'),
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
        $model = ModelRegistry::getByTableName($this->table_name);
        if ($model != null) {
            return Url::toRoute([
                ( isset($model->viewRoute) ? $model->viewRoute : $model->id . '/view'),
                'id' => $this->row_id
            ]);
        } else {
            return null;
        }
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
