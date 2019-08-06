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
        '2019-2020' => [
            'start' => '2019-09-01',
            'end'   => '2020-09-01'
        ],
        '2019-2020 - 1er trimestre' => [
            'start' => '2019-12-10',
            'end'   => '2020-12-01'
        ],
    ]
];
