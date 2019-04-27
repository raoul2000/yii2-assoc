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
            'description' => 'When order value is not set by user, assign the related product value if defined',
            'inputOptions' => ['type' => 'checkbox'],
            'rules' => [
                ['boolean']
            ],
        ],
    ],
];
