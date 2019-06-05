<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

return [
    'name' => $faker->lastname,
    'firstname' => $faker->firstname,
    'is_natural_person' => true,
    'uuid' => $faker->uuid,
    'birthday' => $faker->dateTimeThisCentury->format('Y-m-d'),
    'email' => $faker->email,
    'phone_1' => $faker->PhoneNumber,
    'phone_2' => $faker->PhoneNumber
];
