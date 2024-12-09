<?php

namespace App\Services;

use Predis\Client;

class Redis
{
    protected $redis;

    public function __construct()
    {
        $this->redis = new Client(array_merge([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ], [
            
        ]));
    }

    public function set(string $key, $value, ?int $expiration = null): bool
    {
        $this->redis->set($key, $value);
        if ($expiration) {
            $this->redis->expire($key, $expiration);
        }
        return true;
    }

    public function setex(string $key, $value, int $ttl): void
    {
        $this->redis->setex($key, $ttl, json_encode($value));
    }


    public function get(string $key)
    {
        return $this->redis->get($key);
    }

    public function delete(string $key): bool
    {
        return $this->redis->del([$key]) > 0;
    }

    public function exists(string $key): bool
    {
        return $this->redis->exists($key) > 0;
    }

    public function increment(string $key, int $amount = 1): int
    {
        return $this->redis->incrby($key, $amount);
    }

    public function decrement(string $key, int $amount = 1): int
    {
        return $this->redis->decrby($key, $amount);
    }

    public function keys(string $pattern = '*'): array
    {
        return $this->redis->keys($pattern);
    }

    public function flushAll(): void
    {
        $this->redis->flushall();
    }

    public function getClient(): Client
    {
        return $this->redis;
    }
}
