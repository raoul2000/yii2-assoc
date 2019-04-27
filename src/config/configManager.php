<?php 

return [
    'class' => 'yii2tech\config\Manager',
    'autoRestoreValues' => true, // restore config values from storage at component initialization
    'storage' => [
        'class' => 'yii2tech\config\StorageDb',
        'table' => 'config'
    ],
    'items' => [
        'contact_id' => [
            'value' => null,
            'label' => 'Contact',
            'description' => 'Contact that is used as default for all connected users',
            'rules' => [
                ['integer']
            ],
        ],
        'bank_account_id' => [
            'value' => null,
            'label' => 'Bank Account ',
            'description' => 'The default bank account',
            'rules' => [
                ['integer']
            ],
        ],
        'order.setProductValue' => [
            'value' => false,
            'label' => 'Assign product value to order',
            'description' => 'On order creation, if no value is set by the user, assign the value of the product',
            'inputOptions' => ['type' => 'checkbox'],
            'rules' => [
                ['boolean']
            ],
        ],
    ],
];
