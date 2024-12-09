<?php
namespace App\Database;
use Illuminate\Database\Capsule\Manager as Capsule;

class Message
{
    public static function migrate()
    {
        Capsule::schema()->create('messages', function ($table) {

            $table->id();
            $table->unsignedBigInteger('conversation_id')->nullable(false)->default(NULL)->index();
            $table->unsignedBigInteger('user_id')->nullable(false)->default(NULL)->index();
        
            $table->string('body');
            $table->foreign('conversation_id')->references('id')->on('conversations');
            $table->foreign('user_id')->references('id')->on('users');
                
            $table->timestamps();
        });        
    }
}