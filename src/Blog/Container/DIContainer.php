<?php

namespace src\Blog\Container;

use ReflectionClass;
use Psr\Container\ContainerInterface;

use src\Blog\Exceptions\NotFoundException;

class DIContainer implements ContainerInterface
{
    private array $resolvers = [];
    public function get(string $id): object
    {
        if (array_key_exists($id, $this->resolvers)) {
            $typeToCreate = $this->resolvers[$id];

            if(is_object($typeToCreate)) {
                return $typeToCreate;
            }

            return $this->get($typeToCreate);
        }

        if (!class_exists($id)) {
            throw new NotFoundException("Cannot resolve type: $id");

        }

        $reflectionClass = new ReflectionClass($id);

        $constructor = $reflectionClass->getConstructor();

        if($constructor == null) {
            return new $id();
        }

        $parameters = [];

        foreach ($constructor->getParameters() as $parameter) {
            $parameterType = $parameter->getType()->getName();

            $parameters[] = $this->get($parameterType);
        }

        return new $id(...$parameters);
    }

    public function bind(string $type, $resolver): void
    {
        $this->resolvers[$type] = $resolver;
    }

    public function has(string $id): bool
    {
        try {
            $this->get($id);
        } catch (NotFoundException) {
            return false;
        }

        return true;
    }
}