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
            'start' => '01/09/2019',
            'end'   => '30/06/2020'
        ],
        '1er Trimestre ' => [
            'start' => '01/09/2019',
            'end'   => '31/12/2019'
        ],
        '2nd Trimestre ' => [
            'start' => '01/01/2020',
            'end'   => '31/03/2020'
        ],
        '3ieme Trimestre ' => [
            'start' => '01/04/2020',
            'end'   => '30/06/2020'
        ],
    ]
];
