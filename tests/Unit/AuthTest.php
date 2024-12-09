<?php declare(strict_types=1);
namespace App\Tests;

use App\Container;
use App\Models\Conversation;
use App\Models\User;
use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use Illuminate\Database\Capsule\Manager as Capsule;


class AuthTest extends TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        Capsule::schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('token')->nullable();
            $table->timestamps();
        });
        $this->userRepository = new UserRepository();
    }

    public function testLoginUserNotFound(): void
    {
        $userRepository = new UserRepository();
        $result = $userRepository->login([
            'email' => 'nonexistent@example.com',
            'password' => 'pass',
        ]);

        $this->assertFalse($result);
    }

    public function testLoginValidUser(): void
    {
        $userRepository = new UserRepository();
        $user = User::create([
            'email' => 'user@example.com',
            'password' => $userRepository->hashPassword('pass')
        ]);

        $result = $userRepository->login([
            'email' => 'user@example.com',
            'password' => 'pass',
        ]);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);

        $updatedUser = User::find($user->id);
        $this->assertEquals($result, $updatedUser->token);
    }
}
