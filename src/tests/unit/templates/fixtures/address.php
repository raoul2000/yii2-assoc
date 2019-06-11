<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

return [
    'id' => $index + 1,
    'line_1' => $faker->streetAddress,
    'line_2' => ( $faker->numberBetween(0, 5) == 0 ? null : $faker->secondaryAddress),
    'zip_code' => $faker->postcode,
    'city' => $faker->city,
    'country' => $faker->country
];
