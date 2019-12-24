<?php

namespace app\modules\gymv\models;

class MemberQuery extends \app\models\ContactQuery
{
    public function init()
    {
        $this->andOnCondition(['is_natural_person' => true]);
        parent::init();
    }

    public function active($state = true)
    {
        return $this->andOnCondition(['active' => $state]);
    }
}
