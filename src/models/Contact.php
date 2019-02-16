<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contact".
 *
 * @property string $id
 * @property string $name name
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
    public function rules()
    {
        return [
            [['id','name'], 'required'],
            [['id'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 128],
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
            'name' => 'name',
        ];
    }
}
