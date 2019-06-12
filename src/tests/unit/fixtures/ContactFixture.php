<?php

namespace tests\unit\fixtures;

use yii\test\ActiveFixture;

class ContactFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Contact';
    public $depends = [
        'tests\unit\fixtures\AddressFixture',
        'tests\unit\fixtures\BankAccountFixture',
    ];
}
