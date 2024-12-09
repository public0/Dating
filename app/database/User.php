<?php
namespace App\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class User
{
    public static function migrate()
    {
        Capsule::schema()->create('users', function ($table) {

            $table->id();
        
            $table->string('first_name');
            $table->string('last_name');
            $table->char('sex', 1);
            $table->string('email')->unique();
            $table->string('password');

            $table->string('token', 32)->nullable()->unique();
            $table->timestamps();
        });        
    }
}