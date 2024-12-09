<?php
require_once __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../app/helpers.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Container;
use App\Http\UserController;
use App\Router;
use Illuminate\Pagination\Paginator;

class App {
    private static Container $container;
    private Router $router;
    public function __construct()
    {
        Paginator::currentPageResolver(function ($pageName = 'page') {
            return $_GET['page'] ?? 1;
        });
        
        $capsule = new Capsule();

        $capsule->addConnection([
            "driver" => "mysql",
            "host"   => "127.0.0.1",
            "database" => "date_test",
            "username" => "root",
            "password" => "pass"
         ]);
        
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        self::$container = new Container();
        $this->router = new Router(self::$container);

        $this->router->get('/migrate', [UserController::class, 'migrate']);
        $this->router->post('/login', [UserController::class, 'login']);
        $this->router->post('/register', [UserController::class, 'register']);
        $this->router->get('/user', [UserController::class, 'show']);
        $this->router->get('/search', [UserController::class, 'search']);
        $this->router->post('/message', [UserController::class, 'sendMessage']);
        $this->router->get('/messages', [UserController::class, 'messages']);
        $this->router->get('/profiles', [UserController::class, 'topProfiles']);

    }

    public function run(): void
    {
        $this->router->resolve($_SERVER['REQUEST_URI'], strtolower($_SERVER['REQUEST_METHOD']));
    }
}

