<?php
$contactRelations = require __DIR__ . '/contact-relations.php';

return [
    'adminEmail'        => 'admin@example.com',
    'fluid-layout'      => false,
    'contact-relations' => $contactRelations,
    /**
     * Date format used by Date Validators when validating user input
     * User must enter date following this format
     */
    'dateValidatorFormat' => 'dd/MM/yyyy',
    /**
     * Configured Date Ranges
     * key : name of the date range
     * value : Array with key 'start' and 'end'. Each one can be a string or a function returning a string.
     * In both cases the string must be a date formatted like : yyyy-mm-dd
     */
    'dateRange' => [
        // example of hard coded date range
        'saison 2019-2020' => [
            'start' => '2019-09-01',
            'end'   => '2020-08-30'
        ],
        'saison en cours' => [
            'start' => function() {
                $now = new DateTime('now');
                $thisMonth = $now->format('m');
                $thisYear  = $now->format('Y');
                if ( $thisMonth >= 9 && $thisMonth <= 12) {
                    $start = new DateTime('first day of September');
                } else {
                    $start = new DateTime('first day of September ' . ($thisYear - 1));
                }
                return $start->format('Y-m-d');
            },
            'end' => function() {
                $now = new DateTime('now');
                $thisMonth = $now->format('m');
                $thisYear  = $now->format('Y');
                if ( $thisMonth >= 9 && $thisMonth <= 12) {
                    $end = new DateTime('last day of August ' . ($thisYear + 1));
                } else {
                    $end = new DateTime('last day of August');
                }            
                return $end->format('Y-m-d');
            }
        ],
        'This month' => [
            'start' => function() {
                $date = new DateTime('first day of this month');
                return $date->format('Y-m-d');
            },
            'end' => function() {
                $date = new DateTime('last day of this month');
                return $date->format('Y-m-d');
            }
        ],
        'Last month' => [
            'start' => function() {
                $date = new DateTime('first day of last month');
                return $date->format('Y-m-d');
            },
            'end' => function() {
                $date = new DateTime('last day of last month');
                return $date->format('Y-m-d');
            }
        ],
        'Next month' => [
            'start' => function() {
                $date = new DateTime('first day of next month');
                return $date->format('Y-m-d');
            },
            'end' => function() {
                $date = new DateTime('last day of next month');
                return $date->format('Y-m-d');
            }
        ],
    ],
    /**
     * Product Group For Registration
     * 
     * During registration wizard, the product selection panel displays product in two ways depending the group 
     * they belong to : 
     * 
     * - group-1 : products are displayed as a checkbox list
     * - group-2 : products are searchable through a auto completion input control
     * 
     * To configure a group, assign an map with 2 keys : 
     * 
     * - modelId : an array containing a list of product Ids. All products these products will be included in the group
     * - categoryId : list of product category ids. All products belonging to these categories will be included in the group
     */
    'registration.product.group' => [
        'group-1' => [
            'productId' => [11,12, 13],
            //'categoryId' => [1]
        ],
        'group-2' => [
            'categoryId' => [10]            
        ],
    ],
    // transaction created during registration are assigned this category Id
    'registration.transaction.categoryId' => 3
];
