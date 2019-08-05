<?php

namespace app\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\base\InvalidCallException;
use \app\components\helpers\DateHelper;

class DateConverterBehavior extends Behavior
{
    public $attributes = [];

    private $_dbFormats = [];
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'convertDbToApp',
            ActiveRecord::EVENT_BEFORE_INSERT => 'convertAppToDb',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'convertAppToDb',

            ActiveRecord::EVENT_AFTER_INSERT => 'convertDbToApp',
            ActiveRecord::EVENT_AFTER_UPDATE => 'convertDbToApp',
        ];
    }

    public function init()
    {
        parent::init();
    }
    
    public function convertDbToApp($event)
    {
        foreach ($this->attributes as $attributeName) {
            if (!empty($this->owner->$attributeName)) {
                $this->_dbFormats[$attributeName] = $this->owner->$attributeName;
                $this->owner->$attributeName = DateHelper::toDateAppFormat($this->owner->$attributeName);
            }
        }
    }

    public function convertAppToDb($event)
    {
        foreach ($this->attributes as $attributeName) {
            if (!empty($this->owner->$attributeName)) {
                $this->owner->$attributeName = DateHelper::toDateDbFormat($this->owner->$attributeName);
            }
        }
        // should we convert back to app format after save ?
    }
}
