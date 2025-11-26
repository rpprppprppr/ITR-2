<?php

namespace src\Blog\UnitTests\Dummies;

use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\Exceptions\UserNotFoundException;
use src\Blog\Person\Name;
use src\Blog\User;
use src\Blog\UUID;

class DummyUserRepository implements UserRepositoryInterface
{
    public function save(User $user): void
    {

    }

    public function get(UUID $uuid): User
    {
        throw new UserNotFoundException("Not Found");
    }

    public function getByUsername(string $username): User
    {
        return new User(UUID::random(), "Ivan", new Name('first', 'last'));
    }
}