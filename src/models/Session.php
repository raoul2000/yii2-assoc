<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "session".
 *
 * @property string $id
 * @property int $expire
 * @property resource $data
 * @property int $user_id
 * @property int $last_write
 */
class Session extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'session';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['expire', 'user_id', 'last_write'], 'integer'],
            [['data'], 'string'],
            [['id'], 'string', 'max' => 40],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'expire' => 'Expire',
            'data' => 'Data',
            'user_id' => 'User ID',
            'last_write' => 'Last Write',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\Da\User\Model\User::className(), ['id' => 'user_id']);
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
        $sessions = parent::find()
            ->select('user_id')
            ->distinct()
            ->with(['user'])
            ->asArray()
            ->andWhere(['NOT', 'user_id IS NULL'])
            ->all();

        return ArrayHelper::map($sessions, 'user_id', function ($item) {
            return $item['user']['username'];
        });
    }
}
