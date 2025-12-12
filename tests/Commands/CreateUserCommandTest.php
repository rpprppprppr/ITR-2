<?php

namespace src\Blog\UnitTests\Commands;

use PHPUnit\Framework\TestCase;

use src\Blog\Commands\Arguments;
use src\Blog\Commands\CreateUserCommand;

use src\Blog\Exceptions\ArgumentException;
use src\Blog\Exceptions\CommandException;
use src\Blog\Exceptions\UserNotFoundException;

use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use src\Blog\UnitTests\DummyLogger;
use src\Blog\User;
use src\Blog\UUID;
use src\Blog\Person\Name;

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

    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $usersRepository = new class implements UserRepositoryInterface {

            public function save(User $user): void {}

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("User not found: $uuid");
            }

            public function getByUsername(string $username): User
            {
                return new User(UUID::random(), $username, new Name("First", "Last"));
            }

            public function delete(UUID $uuid): void {}
        };

        $command = new CreateUserCommand($usersRepository, new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User already exists: Ivan");

        $command->handle(new Arguments([
            "username" => "Ivan",
            "first_name" => "test",
            "last_name" => "test"
        ]));
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument: first_name");

        $command->handle(new Arguments(["username" => "Ivan"]));
    }

    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument: last_name");

        $command->handle(new Arguments(["username" => "Ivan", "first_name" => "Ivan"]));
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

        $command = new CreateUserCommand($usersRepository, new DummyLogger());

        $command->handle(new Arguments([
            "username" => "Ivan",
            "first_name" => "Ivan",
            "last_name" => "Ivanov"
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }
}