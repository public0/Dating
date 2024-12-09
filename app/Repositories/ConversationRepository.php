<?php
namespace App\Repositories;

use App\Models\User;

class ConversationRepository
{
    public function __construct()
    {
        # code...
    }
    public function register(array $data): User
    {
        return User::Create(
            [
                'name' => "Ahmed Khan",
                'email' => "alex3.khan@lbs.com",
                'password' => hash("md5",'alex.khan@lbs.com')
            ]
        );
    }
}
