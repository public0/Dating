<?php
namespace App\Http;

use App\Database\Conversation;
use App\Database\Message;
use App\Database\Seeders\DatabaseSeeder;
use App\Database\User;
use App\Exceptions\HttpException;
use App\Repositories\UserRepository;
use App\Services\Redis;
use Illuminate\Database\Capsule\Manager as Capsule;

class UserController extends Controller {

    public function register()
    {
        $result = $this->container->get(UserRepository::class)->register([
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'sex' => $_POST['sex'],
            'password' => $_POST['password']
        ]);
        return response()->json(['registered' => $result]);
    }

    public function login()
    {
        $result = $this->container->get(UserRepository::class)->login([
            'email' => $_POST['email'],
            'password' => $_POST['password'],
        ]);

        return response()->json(['token' => $result]);
    }

    public function show()
    {
        if(!$this->isLogged) {
            throw new HttpException("Unauthorized", 401);
        }

        return response()->json(['user' => $this->user]);
    }

    public function topProfiles()
    {
        $result = $this->container->get(UserRepository::class)->topProfiles(
            $this->container->get(Redis::class)
        );

        return response()->json(['results' => $result]);
    }

    public function messages()
    {
        if(!$this->isLogged) {
            throw new HttpException("Unauthorized", 401);
        }

        $result = $this->container->get(UserRepository::class)->messages(
            $this->user,
            $_GET['id']
        );

        return response()->json($result);
    }

    public function sendMessage()
    {
        if(!$this->isLogged) {
            throw new HttpException("Unauthorized", 401);
        }

        if(!isset($_POST['to'])) {
            throw new HttpException("Invalid request", 400);
        }

        $result = $this->container->get(UserRepository::class)->sendMessage(
            $this->user,
            $_POST['message'] ?? '',
            $_POST['to']
        );
        return response()->json(['user' => $result]);
    }

    public function search()
    {
        if(!$this->isLogged) {
            throw new HttpException("Unauthorized", 401);
        }

        $result = $this->container->get(UserRepository::class)->search(
            $_GET['term'] ?? ''
        );
        
        return response()->json(['users' => $result]);
    }

    public function migrate()
    {
        Capsule::schema()->dropAllTables();
        User::migrate();
        Conversation::migrate();
        Message::migrate();
        $this->container->get(DatabaseSeeder::class)->run();

    }
}
