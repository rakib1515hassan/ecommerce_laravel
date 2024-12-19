<?php

/** @var Factory $factory */

use App\Models\BlogComment;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(BlogComment::class, function (Faker $faker) {
    return [
        'comment' => Str::random(10),
        'is_approved' => '1',
        'blog_id' => '1',
        'created_by_id' => '1',
    ];
});
