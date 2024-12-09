<?php
namespace App\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class Conversation
{
    public static function migrate()
    {
        Capsule::schema()->create('conversations', function ($table) {
            $table->id();

            $table->unsignedBigInteger('user1_id')->index();
            $table->unsignedBigInteger('user2_id')->index();

            $table->foreign('user1_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user2_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();

            $table->unique(['user1_id', 'user2_id'], 'unique_user_pair');
        });
    }
}
