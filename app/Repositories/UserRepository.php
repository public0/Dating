<?php
namespace App\Repositories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\Redis;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Database\Capsule\Manager as Capsule;

class UserRepository
{
    public function register(array $data): User | bool
    {
        try {
            return User::insert([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'sex' => $data['sex'],
                'password' =>  $this->hashPassword($data['password'])
            ]);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function login(array $data): bool | string
    {
        $user = User::where(['email' => $data['email']])->first();
        if(!$user) {
            return false;
        }

        $valid = $this->verifyPassword($data['password'], $user->password);

        if(!$valid) {
            return false;
        }

        $token = $this->generateToken($data['email'].Str::random(10));
        $user->update(['token' => $token]);

        return $token ?? false;

    }

    public function show($token): bool | string
    {
        return User::where(['token' => $token])->first();
    }

    public function topProfiles(Redis $redis): mixed
    {
        $cacheKey = 'top_profiles';
        $cacheTtl = 600;

        $results = $redis->get($cacheKey);
        
        if (!$results) {
            $results = Capsule::select("
                SELECT 
                    u.id AS user_id,
                    u.first_name,
                    u.last_name,
                    COUNT(c.id) AS conversation_count
                FROM 
                    users u
                LEFT JOIN 
                    conversations c
                ON 
                    u.id = c.user1_id OR u.id = c.user2_id
                GROUP BY 
                    u.id, u.first_name, u.last_name
                ORDER BY 
                    conversation_count DESC
                LIMIT 5
            ");
    
            $redis->setex($cacheKey, $results, $cacheTtl);
        } else {
            $results = json_decode($results, true);
        }
    
        return $results;
    }
    
    public function messages(User $user, int $id): array | null
    {
        $user1_id = min($user->id, $id);
        $user2_id = max($user->id, $id);

        $conversation = Conversation::where(['user1_id' => $user1_id, 'user2_id' => $user2_id])->first();

        if ($conversation) {
            $messages = $conversation->messages()->paginate(10);
            return [
                'conversation' => $conversation,
                'messages' => $messages,
            ];
        }

        return null;
    }

    public function sendMessage(User $user, string $message, int $to): Message
    {
        /**
         * Consensus that in a conversation between 2 people the one with the smallest id is always user1_id 
         */
        $user1_id = min($user->id, $to);
        $user2_id = max($user->id, $to);
        $conversation = Conversation::where(['user1_id' => $user1_id])->first();
        if(!$conversation) {
            $conversation = Conversation::Create(
                [
                    'user1_id' => $user1_id,
                    'user2_id' => $user2_id,
                ]
            );
        }
        
        return Message::Create(
            [
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'body' => $message,
            ]
        );
    }

    public function search($term = ''): Collection
    {
        // Use elasticsearch
        return User::where('first_name', 'LIKE', "%{$term}%")
            ->orWhere('last_name', 'LIKE', "%{$term}%")
        ->get();
    }

    public function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function generateToken(string $string): string {
        return hash("md5", $string);
    }

    public function verifyPassword($plainPassword, $hashedPassword): bool {
        return password_verify($plainPassword, $hashedPassword);
    }
}
