<?php

namespace src\Blog\UnitTests\Commands;

use PHPUnit\Framework\TestCase;
use src\Blog\Commands\Users\CreateUser;
use src\Blog\Exceptions\UserNotFoundException;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\User;
use src\Blog\UUID;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateUserCommandTest extends TestCase
{
    private function makeUsersRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface
        {
            protected bool $called = false;

            public function save(User $user): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function delete(UUID $uuid): void {}
        };
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser($this->makeUsersRepository());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "first_name").');

        $command->run(
            new ArrayInput([
                "username" => "Ivan",
                "password" => "123",
                "last_name" => "Ivanov"
            ]),
            new NullOutput()
        );
    }

    public function testItRequiresLastName(): void
    {
        $command = new CreateUser($this->makeUsersRepository());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "last_name").');

        $command->run(
            new ArrayInput([
                "username" => "Ivan",
                "password" => "123",
                "first_name" => "Ivan"
            ]),
            new NullOutput()
        );
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUser($this->makeUsersRepository());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "password").');

        $command->run(
            new ArrayInput([
                "username" => "Ivan",
                "first_name" => "Ivan",
                "last_name" => "Ivanov",
            ]),
            new NullOutput()
        );
    }

    public function testItSavesUserToRepository(): void
    {
        $usersRepository = new class implements UserRepositoryInterface {
            private bool $called = false;

            public function save(User $user): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("User not found: $uuid");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("User not found: $username");
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }

            public function delete(UUID $uuid): void {}
        };

        $command = new CreateUser($usersRepository);

        $command->run(
            new ArrayInput([
                "username"   => "Ivan",
                "password"   => "123",
                "first_name" => "test",
                "last_name"  => "test"
            ]),
            new NullOutput()
        );

        $this->assertTrue($usersRepository->wasCalled());
    }
}