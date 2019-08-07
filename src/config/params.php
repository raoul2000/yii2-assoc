<?php
$contactRelations = require __DIR__ . '/contact-relations.php';

return [
    'adminEmail' => 'admin@example.com',
    'fluid-layout' => false,
    'contact-relations' => $contactRelations,
    /**
     * Date format used by Date Validators whe validating user input
     * User must enter date following this format
     */
    'dateValidatorFormat' => 'dd/MM/yyyy',
    /**
     * Configured Date Ranges
     */
    'dateRange' => [
        'saison 2019-2020' => [
            'start' => '2019-09-01',
            'end'   => '2020-06-30'
        ],
        '1er Trimestre ' => [
            'start' => '2019-09-01',
            'end'   => '2020-01-01'
        ],
    ]
];
