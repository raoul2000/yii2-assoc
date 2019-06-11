<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

 use app\models\Contact;

 $is_natural_person = $faker->randomElement([true,  false]);

// 50% chance Male / femaler gender
$gender = $faker->randomElement([Contact::GENDER_MALE, Contact::GENDER_FEMALE]);

// 1 chance over 5 to have no email
$has_email = $faker->numberBetween(0, 5) !== 0;

if ($is_natural_person) {
    return [
        'id' => $index + 1,
        'name' => $faker->lastname,
        'firstname' => $faker->firstname,
        'gender' => $gender,
        'is_natural_person' => $is_natural_person,
        'uuid' => $faker->uuid,
        'birthday' => $faker->dateTimeThisCentury->format('Y-m-d'),
        'email' => ($has_email ? $faker->email : null),
        'phone_1' => $faker->PhoneNumber,
        'phone_2' => $faker->PhoneNumber,
        'address_id' =>  $index + 1
    ];
} else {
    return [
        'id' => $index + 1,
        'name' => $faker->company,
        'firstname' => '',
        'is_natural_person' => $is_natural_person,
        'uuid' => $faker->uuid,
        'email' => ($has_email ? $faker->email : null),
        'phone_1' => $faker->PhoneNumber,
        'phone_2' => $faker->PhoneNumber,
        'address_id' =>  $index + 1
    ];
}

