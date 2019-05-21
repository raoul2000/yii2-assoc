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
            'value' => 0,
            'label' => 'Contact',
            'description' => 'ID of the Contact that is used as default for all connected users',
            'rules' => [
                ['integer']
            ],
        ],
        'bank_account_id' => [
            'value' => 0,
            'label' => 'Bank Account ',
            'description' => 'ID of the Bank Account owned by the Default Contact, and that is used for all connected users',
            'rules' => [
                ['integer']
            ],
        ],
        'order.create.setProductValue' => [
            'value' => false,
            'label' => 'Assign product value to order',
            'description' => 'On order creation, if no value is set by the user, assign the value of the product',
            'inputOptions' => ['type' => 'checkbox'],
            'rules' => [
                ['boolean']
            ],
        ],
        'order.create.setDefaultValidity' => [
            'value' => false,
            'label' => 'Apply current date range on Order creation',
            'description' => 'When you ceate an order, if a date range is active, it is used to populate validity fields',
            'inputOptions' => ['type' => 'checkbox'],
            'rules' => [
                ['boolean']
            ],
        ],
        'product.create.setDefaultValidity' => [
            'value' => false,
            'label' => 'Apply current date range on Product creation',
            'description' => 'When you ceate a product, if a date range is active, it is used to populate validity fields',
            'inputOptions' => ['type' => 'checkbox'],
            'rules' => [
                ['boolean']
            ],
        ],
    ],
];
