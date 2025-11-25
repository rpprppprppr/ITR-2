<?php

namespace src\Blog\Repositories\UsersRepository;

use src\Blog\UUID;
use src\Blog\User;
use src\Blog\Exceptions\UserNotFoundException;

class InMemoryUserRepository implements UserRepositoryInterface
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if ((string)$user->getId() === (string)$uuid) {
                return $user;
            }
        }

        throw new UserNotFoundException("User not found: $uuid");
    }

    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($user->getUsername() === $username) {
                return $user;
            }
        }

        throw new UserNotFoundException("User not found: $username");
    }
}