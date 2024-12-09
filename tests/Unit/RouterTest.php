<?php declare(strict_types=1);
namespace App\Tests;

use App\Container;
use PHPUnit\Framework\TestCase;
use App\Http\UserController;
use App\Router;

class RouterTest extends TestCase
{
    protected $container;
    protected $router;

    protected function setUp(): void 
    {
        parent::setUp();
        $this->container = new Container();
        $this->router = new Router($this->container);
    }

    public function testRegisterGetRoute(): void 
    {

        $this->router->get('/test', [UserController::class, 'test']);

        $expected = [
            'get' => [
                "/public/test" => [
                    'App\Http\UserController',
                    'test'
                ]
            ]
        ];

        $this->assertEquals($expected, $this->router->routes());
    }

    public function testRegisterPostRoute(): void 
    {
        $this->router->post('/test', [UserController::class, 'test']);

        $expected = [
            'post' => [
                "/public/test" => [
                    'App\Http\UserController',
                    'test'
                ]
            ]
        ];

        $this->assertEquals($expected, $this->router->routes());
    }
}
