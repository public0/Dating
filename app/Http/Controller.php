<?php
namespace App\Http;
use App\Container;
use App\Models\User;

class Controller {

    protected $isLogged;

    protected $user;

    public function __construct(protected Container $container) {
        $this->validateToken();
    }
    
    private function validateToken() {
        $headers = getallheaders();
        if(!isset($headers['Authorization'])) {
            $this->isLogged = false;
            return;
        }
        $token = $token = substr($headers['Authorization'], 7);
        $this->user = User::where(['token' => $token])->first();
        $this->isLogged = $this->user ? true : false;        
    }
}