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
    'dateValidatorFormat' => 'dd/MM/yyyy'
];
