<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use bupy7\activerecord\history\behaviors\History as HistoryBehavior;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property string $valid_date_start
 * @property string $valid_date_end
 * @property string $description
 * @property int $created_at timestamp of record creation (see TimestampBehavior)
 * @property int $updated_at timestamp of record last update (see TimestampBehavior)
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
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
            [
                'class' => \app\components\behaviors\DateConverterBehavior::className(),
                'attributes' => [
                    'valid_date_start',
                    'valid_date_end'
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
            [['name', 'value'], 'required'],
            [['value'], 'number', 'min' => 0],
            [['name'], 'string', 'max' => 45],
            [['description'], 'safe'],
            [['name'], 'unique'],

            // Validity Date Range ///////////////////////////////////////////////////
            
            [['valid_date_start', 'valid_date_end'], 'date',  'format' => Yii::$app->params['dateValidatorFormat']],
            ['valid_date_start', \app\components\validators\DateRangeValidator::className()],
        ];
    }

    public function validateDates()
    {
        if ($this->hasErrors('valid_date_start') || $this->hasErrors('valid_date_end')) {
            return;
        }
        if (!empty($this->valid_date_end) && !empty($this->valid_date_start)) {

            $start = \app\components\helpers\DateHelper::toDateDbFormat($this->valid_date_start);
            $end   = \app\components\helpers\DateHelper::toDateDbFormat($this->valid_date_end);
    
            if (strtotime($end) < strtotime($start)) {
                $this->addError('valid_date_start', 'Please give correct Start and End dates');
                $this->addError('valid_date_end', 'Please give correct Start and End dates');
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'valid_date_start' => 'Valid Date Start',
            'valid_date_end' => 'Valid Date End',
        ];
    }
    /**
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::beforeDelete()
     */
    public function beforeDelete()
    {
        foreach ($this->orders as $order) {
            $order->delete();
        }
        return true;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['product_id' => 'id']);
    }
    /**
     * Returns an array containing all product names indexed by contact Id.
     *
     * @returns array list of [id, name] items
     */
    public static function getNameIndex()
    {
        $products = parent::find()
            ->select(['id','name'])
            ->asArray()
            ->all();
        return ArrayHelper::map($products, 'id', 'name');
    }
}
