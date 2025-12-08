<?php

namespace src\Blog\UnitTests\Container;

use PHPUnit\Framework\TestCase;

use src\Blog\Container\DIContainer;

use src\Blog\Exceptions\NotFoundException;
use src\Blog\Repositories\UsersRepository\InMemoryUserRepository;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

class DIContainerTest extends TestCase
{
    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        $container = new DIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Cannot resolve type: src\Blog\UnitTests\Container\SomeClass");

        $container->get(SomeClass::class);
    }

    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = new DIContainer();

        $object = $container->get(SomeClassWithoutDependencies::class);

        $this->assertInstanceOf(SomeClassWithoutDependencies::class, $object);
    }

    public function testItResolvesClassByContract(): void
    {
        $container = new DIContainer();

        $container->bind(UserRepositoryInterface::class, InMemoryUserRepository::class);

        $object = $container->get(UserRepositoryInterface::class);

        $this->assertInstanceOf(InMemoryUserRepository::class, $object);
    }

    public function testItReturnPredefinedObject(): void
    {
        $value = 12;
        $container = new DIContainer();

        $container->bind(SomeClassWithParameter::class, new SomeClassWithParameter($value));

        $object = $container->get(SomeClassWithParameter::class);

        $this->assertInstanceOf(SomeClassWithParameter::class, $object);
        $this->assertSame($value, $object->getValue());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        $value = 12;
        $container = new DIContainer();

        $container->bind(SomeClassWithParameter::class, new SomeClassWithParameter($value));

        $object = $container->get(SomeClassDependingOnAnother::class);

        $this->assertInstanceOf(SomeClassDependingOnAnother::class, $object);
    }
}