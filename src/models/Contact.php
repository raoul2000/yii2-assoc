<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use bupy7\activerecord\history\behaviors\History as HistoryBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii2tech\ar\softdelete\SoftDeleteQueryBehavior;

/**
 * This is the model class for table "contact".
 *
 * @property string $id
 * @property string $name name
 * @property int $created_at timestamp of record creation (see TimestampBehavior)
 * @property int $updated_at timestamp of record last update (see TimestampBehavior)
 * @property bool $is_deleted used by soft delete behavior to flag a record as soft deleted
 */
class Contact extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact';
    }
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => HistoryBehavior::className(),
                'skipAttributes' => [
                    'created_at',
                    'updated_at',
                ],
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],            
        ];
    }

    public static function find()
    {
        $query = parent::find();
        // Hard code soft delete condition because I could not use 
        // the SoftDeleteQueryBehavior behavior described in https://github.com/yii2tech/ar-softdelete
        //    $query->attachBehavior('softDelete', SoftDeleteQueryBehavior::className());
        $query->andWhere([ 'is_deleted' => 0]);
        return $query;
    }    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','name'], 'required'],
            [['id'], 'unique'],
            [['created_at', 'updated_at','is_deleted'], 'integer'],
            [['id'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',     
            'is_deleted' => 'Is Deleted'       
        ];
    }
}
