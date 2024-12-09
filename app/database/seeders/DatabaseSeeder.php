<?php
 
namespace App\Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        Capsule::table('users')->insert([[
            'first_name' => 'Julius',
            'last_name' => 'Caesar',
            'sex' => 'F',
            'email' => 'veni.vidi.vici@gmail.com',
            'token' => '142594708f3a5a3ac2980914a0fc954f',
            'password' => '123',
        ],[
            'first_name' => Str::random(10),
            'last_name' => Str::random(10),
            'sex' => 'M',
            'email' => Str::random(10).'@gmail.com',
            'token' => hash("md5", Str::random(10).'@gmail.com'),
            'password' => '123',
        ],[
            'first_name' => Str::random(10),
            'last_name' => Str::random(10),
            'sex' => 'M',
            'email' => $email = Str::random(10).'@gmail.com',
            'token' => hash("md5", Str::random(10).'@gmail.com'),
            'password' => '123',
        ]]);
        
    }
}