<?php
namespace App;

use Exception;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface {

    private array $entries = [];

    public function get(string $id)
    {
        if($this->has(($id))) {
            $entry = $this->entries[$id];    
            return $entry($this);
        }

        return $this->resolve($id);

    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, callable $concrete): void
    {
        $this->entries[$id] = $concrete;
    }

    public function resolve(string $id)
    {
        $reflectionClass = new \ReflectionClass($id);

        if(!$reflectionClass->isInstantiable()) {
            throw new Exception('Class '.$id.' not instantiable!');
        }

        $constructor = $reflectionClass->getConstructor();

        if(!$constructor)
            return new $id;

        $params = $constructor->getParameters();
        
        if(!$params)
            return new $id;

        $dependencies = array_map(function(\ReflectionParameter $param) use ($id) {
            $name = $param->getName();
            $type = $param->getType();

            if(!$type)
                throw new Exception('Class '.$id.' type hint is missing for '.$name);

            if($type instanceof \ReflectionUnionType)
                throw new Exception('Class '.$id.' has union type which is not supported '.$name);

            if($type instanceof \ReflectionNamedType && $type->isBuiltin()) {
                return $this->get($type->getName());
            }

            throw new Exception('Failed to resolve '.$id.'  '.$name.' is invalid!');

        }, $params);

        return $reflectionClass->newInstanceArgs($dependencies);
    }

}